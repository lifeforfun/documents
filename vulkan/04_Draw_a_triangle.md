### 画一个三角形
* [设置](04_Draw_a_triangle.md#基础代码)
    * [基础代码](04_Draw_a_triangle.md#基础代码)
    * [实例](04_Draw_a_triangle.md#实例)
    * [验证层](TODO)
    * [物理设备和队列簇](TODO)
    * [逻辑设备和队列](TODO)
* [外观](TODO)
    * [窗口层](TODO)
    * [交换链](TODO)
    * [图像视图](TODO)
* [图形管道基础](TODO)
    * [介绍](TODO)
    * [着色器模块](TODO)
    * [固定函数](TODO)
    * [渲染通道](TODO)
    * [结论](TODO)
* [画图](TODO)
    * [帧缓冲](TODO)
    * [命令缓冲](TODO)
    * [渲染和展示](TODO)
* [交换链再造](TODO)

#### 基础代码
>   * [通用结构](04_Draw_a_triangle.md#通用结构)
>   * [资源管理](04_Draw_a_triangle.md#资源管理)
>   * [集成GLFW](04_Draw_a_triangle.md#集成GLFW)

##### 通用结构
   上一章我们已经正确配置并创建了一个Vulkan项目并且已经经过了一些代码测试。
这一章我们会以以下代码从头开始编写：
```c++
#include <vulkan/vulkan.h>

#include <iostream>
#include <stdexcept>
#include <functional>
#include <cstdlib>

class HelloTriangleApplication {
public:
    void run() {
        initVulkan();
        mainLoop();
        cleanup();
    }

private:
    void initVulkan() {

    }

    void mainLoop() {

    }

    void cleanup() {

    }
};

int main() {
    HelloTriangleApplication app;

    try {
        app.run();
    } catch (const std::exception& e) {
        std::cerr << e.what() << std::endl;
        return EXIT_FAILURE;
    }

    return EXIT_SUCCESS;
}
```
我们首先引入了LunarG SDK的Vulkan头文件，它提供了函数、结构体和枚举类型。`stdexcept`和`iostream`头文件用于错误报告和传递。
`functional`头文件用于资源管理小节里的lambda函数。`cstdlib`提供了`EXIT_SUCCESS`和`EXIT_FAILURE`宏。

这个程序包裹进了一个类中，Vulkan的若干对象以类私有成员存放进去并为它们编写了初始化方法`initVulkan`。
一旦一切准备就绪我们就进入到主循环中开始渲染帧。
我们以一个在关闭窗口时才会终止迭代的循环填充`mainLoop`方法。
一旦窗口关闭`mainLoop`就会返回，我们使用`cleanup`方法来确保资源释放。

如果有任意致命错误在运行时产生，一个`std::runtime_error`异常将会抛出来，里面携带了描述信息。这个异常会传播至`main`函数并打印到命令提示符里。
要捕获各种标准异常的话，我们可以捕获更通用的`std::exception`异常。
一个接下来我们要处理的错误是找出哪个扩展不被支持的问题。

这之后的每一章大概都会新增一个在`initVulkan`里调用的方法并且类的私有成员中的若干Vulkan对象需要在最后的`cleanup`释放。

##### 资源管理
   就像每块`malloc`分配的内存需要调用`free`一样，每个Vulkan对象需要在使用完毕后显示释放。
现代C++可以利用`<memory>`头文件实现自动资源管理，但是这个指南中我们选用显示分配和释放Vulkan对象。
但毕竟Vulkan的定位是显示操作以防止失误，所以最好是显示指定对象生命周期来理解API如何工作的。

学完这个指南后你应该可以通过重载`std::shared_ptr`自己实现一套自动资源管理器。
使用[RAII](https://en.wikipedia.org/wiki/Resource_Acquisition_Is_Initialization)对于大型Vulkan程序来说对你既有利也是推荐方案，
但是为了学习目的的话，能了解幕后实现总是没坏处的。

Vulkan对象要么通过类似[`vkCreateXXX`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkCreateXXX.html)的函数直接创建，
要么通过其他对象的类似[`vkAllocateXXX`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkAllocateXXX.html)的方法分配。
在确保一个对象不再被使用后，你需要使用与其创建方法相对应的[`vkDestroyXXX`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkDestroyXXX.html)和[`vkFreeXXX`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkFreeXXX.html)销毁它。
这些方法的参数通常由对象类型不同而不同，但他们都有一个共同的参数:`pAllocator`。这是一个可选的用于你指定自定义内存分配器的回调函数参数。
在指南中我们会一直忽略此参数并传入`nullptr`。

##### 集成GLFW
   如果仅仅是幕后渲染而不创建窗口Vulkan可以做的很好，但能够确确实实显示出什么东西往往更令人激动！
首先让我们替换`#include <vulkan/vulkan.h>`为：
```c++
#define GLFW_INCLUDE_VULKAN
#include <GLFW/glfw3.h>
```
这样会加载GLFW并且它会自动加载Vulkan头文件。添加一个`initWindow`方法并在`run`方法里其他方法调用前调用它。我们用这个方法来初始化GLFW并创建窗口。
```C++
void run() {
    initWindow();
    initVulkan();
    mainLoop();
    cleanup();
}

private:
    void initWindow() {

    }
```
`initWindow`方法最开始调用的是`glfwInit()`，它会初始化GLFW库。因为GLFW最初是设计为创建一个OpenGL上下文，所以我们需要告诉它不要在后续调用里创建OpenGL上下文:
```C++
glfwWindowHint(GLFW_CLIENT_API, GLFW_NO_API);
```
由于调整窗口大小需要特殊处理，我们在此先通过另一个hint方法调用禁用它：
```C++
glfwWindowHint(GLFW_RESIZABLE, GLFW_FALSE);
```
剩下的就只有创建一个窗口了。添加一个`GLFWwindow* window;`私有成员来存储引用并使用以下代码初始化:
```C++
window = glfwCreateWindow(800, 600, "Vulkan", nullptr, nullptr);
```
前三个参数指定了窗口的宽、高和标题。第四个参数可选指定一个用于显示窗口的显示器，最后一个参数和OpenGL相关。

最好使用常量来标识宽高数字来替代硬编码，因为之后我们还要使用若干次这些值。我在`HelloTriangleApplication`类定义前面添加了下列代码：
```C++
const int WIDTH = 800;
const int HEIGHT = 600;
```
同时替换窗口创建函数调用为:
```C++
window = glfwCreateWindow(WIDTH, HEIGHT, "Vulkan", nullptr, nullptr);
```
现在你的`initWindow`方法应该看上去是这样子：
```C++
void initWindow() {
    glfwInit();

    glfwWindowHint(GLFW_CLIENT_API, GLFW_NO_API);
    glfwWindowHint(GLFW_RESIZABLE, GLFW_FALSE);

    window = glfwCreateWindow(WIDTH, HEIGHT, "Vulkan", nullptr, nullptr);
}
```
要保持应用运行直到遇到一个错误或者窗口关闭才退出，我们需要使用一个事件循环方法`mainLoop`，如下：
```C++
void mainLoop() {
    while (!glfwWindowShouldClose(window)) {
        glfwPollEvents();
    }
}
```
这段代码应该见文知义，它循环检测一些事件比如点击了X按钮，直到用户关闭窗口才结束。这也是后续我们渲染帧函数调用时的循环。

一旦窗口关闭，我们需要销毁它并终止GLFW来清理资源占用。这将会是我们第一个`cleanup`代码：
```C++
void cleanup() {
    glfwDestroyWindow(window);

    glfwTerminate();
}
```
当你运行程序你应当能看到一个标题为"Vulkan"的窗口显示出来直到窗口关闭才结束程序。现在我们有了Vulkan应用的骨架程序了，让我们[创建第一个Vulkan项目](TODO)吧！
[C++代码](https://vulkan-tutorial.com/code/00_base_code.cpp)

#### 实例
>   * [创建实例](04_Draw_a_triangle.md#创建实例)
>   * [检测扩展支持](TODO)
>   * [清理工作](TODO)

##### 创建实例
   最先要做的事情就是创建实例来初始化Vulkan库。这个实例连接了你的应用与Vulkan库并且它涉及到关于你的应用对于驱动的一些指定信息。

以新增一个`createInstance`方法开始然后在其内部添加`initVulkan`方法的调用。

```C++
void initVulkan() {
    createInstance();
}
```
