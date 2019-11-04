### 基础代码
>   * [通用结构](04_Draw_a_triangle.md#通用结构)
>   * [资源管理](04_Draw_a_triangle.md#资源管理)
>   * [集成GLFW](04_Draw_a_triangle.md#集成GLFW)

#### 通用结构
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

#### 资源管理
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

#### 集成GLFW