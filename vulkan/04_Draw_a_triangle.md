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
就像每块`malloc`分配的内存需要调用`free`一样，每个Vulkan对象需要在使用完毕后显式释放。
现代C++可以利用`<memory>`头文件实现自动资源管理，但是这个教程中我们选用显式分配和释放Vulkan对象。
但毕竟Vulkan的定位是显式操作以防止失误，所以最好是显式指定对象生命周期来理解API如何工作的。

学完这个教程后你应该可以通过重载`std::shared_ptr`自己实现一套自动资源管理器。
使用[RAII](https://en.wikipedia.org/wiki/Resource_Acquisition_Is_Initialization)对于大型Vulkan程序来说对你既有利也是推荐方案，
但是为了学习目的的话，能了解幕后实现总是没坏处的。

Vulkan对象要么通过类似[`vkCreateXXX`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkCreateXXX.html)的函数直接创建，
要么通过其他对象的类似[`vkAllocateXXX`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkAllocateXXX.html)的方法分配。
在确保一个对象不再被使用后，你需要使用与其创建方法相对应的[`vkDestroyXXX`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkDestroyXXX.html)和[`vkFreeXXX`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkFreeXXX.html)销毁它。
这些方法的参数通常由对象类型不同而不同，但他们都有一个共同的参数:`pAllocator`。这是一个可选的用于你指定自定义内存分配器的回调函数参数。
在教程中我们会一直忽略此参数并传入`nullptr`。

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
另外再添加一个类成员来保存实例的句柄：

```C++
private:
VkInstance instance;
```

现在，为了创建实例我们首先要填充一些关于我们应用的信息的结构体进去。这些数据是可选的，但它们对于优化我们指定的应用有益。
例如它使用了一个有固定特殊行为的知名图形处理引擎。这个结构体叫[`VkApplicationInfo`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/VkApplicationInfo.html):

```C++
void createInstance() {
    VkApplicationInfo appInfo = {};
    appInfo.sType = VK_STRUCTURE_TYPE_APPLICATION_INFO;
    appInfo.pApplicationName = "Hello Triangle";
    appInfo.applicationVersion = VK_MAKE_VERSION(1, 0, 0);
    appInfo.pEngineName = "No Engine";
    appInfo.engineVersion = VK_MAKE_VERSION(1, 0, 0);
    appInfo.apiVersion = VK_API_VERSION_1_0;
}
```

先前提到过Vulkan里许多结构体需要显式提供`sType`成员。还有将来会遇到的用于指向扩展信息的`pNext`成员也是如此，不过在此我们先给它赋值为`nullptr`。

Vulkan的许多信息是通过结构体实例传递的而不是通过函数参数，所以我们需要在创建实例时添加有足够多信息的结构体给它。
下面这个结构体是可选的，它用来告诉Vulkan驱动器我们想使用哪些全局扩展以及验证层。
这里的"全局"指的是应用到整个程序中而不是某个指定的设备。这个会在接下来的若干章节里讲清楚。

```C++
VkInstanceCreateInfo createInfo = {};
createInfo.sType = VK_STRUCTURE_TYPE_INSTANCE_CREATE_INFO;
createInfo.pApplicationInfo = &appInfo;
```

前两个参数看上去很明了，后面两层指定了期望的全局扩展。
"概览"章节已经提到过Vulkan是平台未知的API，所以需要扩展来对接窗口系统。GLFW有一个方便的函数来返回需要的(若干)扩展，我们只需要将结构体传入:

```C++
uint32_t glfwExtensionCount = 0;
const char** glfwExtensions;

glfwExtensions = glfwGetRequiredInstanceExtensions(&glfwExtensionCount);

createInfo.enabledExtensionCount = glfwExtensionCount;
createInfo.ppEnabledExtensionNames = glfwExtensions;
```

最后两个成员决定了启用哪些全局验证层。下一章将深入讲解，现在只需留空就行。

```C++
createInfo.enabledLayerCount = 0;
```

我们已经为创建Vulkan实例指定了全部信息，最后只需调用[`vkCreateInstance`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkCreateInstance.html):

```C++
VkResult result = vkCreateInstance(&createInfo, nullptr, &instance);
```

如你所见，Vulkan里创建对象的函数参数遵循如下模式：
>   * 一个指向创建信息结构体的指针。
>   * 一个自定义分配器回调函数指针，教程里总是`nullptr`。
>   * 一个存储新对象句柄的变量的指针。

如果一切正常，然后捕获我们保存的[`VkInstance`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/VkInstance.html)类成员实例。
几乎所有Vulkan函数都返回一个[`VkResult`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/VkResult.html)类型值，它要么是`VK_SUCCESS`要么是错误码。
要检测实例是否创建成功而不需要保存结果，我们可以直接检测是否是成功值：

```C++
if (vkCreateInstance(&createInfo, nullptr, &instance) != VK_SUCCESS) {
    throw std::runtime_error("failed to create instance!");
}
```

现在运行程序来保证实例可以成功创建。

##### 检测扩展是否被支持
如果你看过[`vkCreateInstance`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkCreateInstance.html)的文档你应该知道一个最可能出现的错误码是`VK_ERROR_EXTENSION_NOT_PRESENT`。
我们可以简单地指定需要的扩展，如果这个错误码又出现就关闭掉。
这对于一些必备的扩展比如窗口接口很重要，但如果我们仅仅想检测下可选的功能呢？

要在创建实例之前获取支持的扩展列表可以使用[`vkEnumerateInstanceExtensionProperties`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkEnumerateInstanceExtensionProperties.html)函数。
它接收一个存储扩展数目的变量指针和一个存储扩展信息的[`VkExtensionProperties`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/VkExtensionProperties.html)的数组。
它还在第一个参数位置可选接收一个让我们指定扩展过滤器的验证层，这里我们先忽略它。

要分配一个存储扩展信息的数组先要知道扩展数量。你可以通过给最后一个参数留空来仅仅请求获取扩展数量：

```C++
uint32_t extensionCount = 0;
vkEnumerateInstanceExtensionProperties(nullptr, &extensionCount, nullptr);
```

现在分配存储扩展信息的数组(`include <vector>`)：

```C++
std::vector<VkExtensionProperties> extensions(extensionCount);
```

最后我们来查询扩展详细信息：

```C++
vkEnumerateInstanceExtensionProperties(nullptr, &extensionCount, extensions.data());
```

每个[`VkExtensionProperties`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/VkExtensionProperties.html)结构体包含名称和扩展版本号。
我们可以在for循环中列出它们(`\t`是一个缩进制表符)：

```C++
std::cout << "available extensions:" << std::endl;

for (const auto& extension : extensions) {
    std::cout << "\t" << extension.extensionName << std::endl;
}
```

如果你想提供一些Vulkan支持的详细信息可以添加代码到`createInstance`方法里。
作为挑战，编写一个方法来检测是否`glfwGetRequiredInstanceExtensions`返回的所有扩展都包含在受支持扩展列表里。

##### 清理工作
[`VkInstance`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/VkInstance.html)应该在紧挨程序退出前销毁。
在`cleanup`方法里使用[`vkDestroyInstance`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkDestroyInstance.html)来销毁它：

```C++
void cleanup() {
    vkDestroyInstance(instance, nullptr);

    glfwDestroyWindow(window);

    glfwTerminate();
}
```

`vkDestroyInstance`方法的参数看起来很直观。前面章节有说过Vulkan里的分配和销毁对应了一个可选的分配器回调，我们通过传入`nullptr`忽略此参数。
所有我们在接下来章节里为Vulkan创建的资源都应该在实例销毁前清理。

在继续我们更复杂的创建实例的步骤前，是时候通过[验证层](TODO)评估下我们的调试选项了。

[C++代码](https://vulkan-tutorial.com/code/01_instance_creation.cpp)


#### 验证层
>   * [什么是验证层？](04_Draw_a_triangle.md#什么是验证层？)
>   * [使用验证层](04_Draw_a_triangle.md#使用验证层)
>   * [消息回调](04_Draw_a_triangle.md#消息回调)
>   * [调试实例创建与销毁](04_Draw_a_triangle.md#调试实例创建于销毁)
>   * [配置](04_Draw_a_triangle.md#配置)

##### 什么是验证层？
Vulkan的设计理念是保持最小的驱动负载，达到这个目的的其中一点就是默认它只有有限的错误检测。
即便是像是给枚举类型一个错误值或者给必传参数一个空指针这种简单错误通常都不会显式捕获出来而是会导致程序崩溃或者未知行为。
因为Vulkan需要你完全显式地做开发，所以一些很小的错误比如使用一个新的GPU特性而忘记在逻辑设备创建时发起请求很容易会出现。

但这不意味着这些检测无法被加入到API里。Vulkan为此引入了一个优雅的系统：验证层。
验证层是一些可选组件，是用于Vulkan函数调用执行一些操作的钩子方法。常见的验证层操作有：
>   * 依据说明书检测参数值是否被错误使用
>   * 追踪对象创建和销毁来发现资源泄漏
>   * 追踪线程调用检测线程安全
>   * 记录每个调用及其参数到标准输出
>   * 跟踪Vulkan调用做采集和报告

这里给出一个分析验证层的实现：

```C++
VkResult vkCreateInstance(
    const VkInstanceCreateInfo* pCreateInfo,
    const VkAllocationCallbacks* pAllocator,
    VkInstance* instance) {

    if (pCreateInfo == nullptr || instance == nullptr) {
        log("Null pointer passed to required parameter!");
        return VK_ERROR_INITIALIZATION_FAILED;
    }

    return real_vkCreateInstance(pCreateInfo, pAllocator, instance);
}
```

验证层可以自由地堆叠到你感兴趣的调试功能里。你可以轻松地在调试构建时启用而在发布时禁用，给你最大的自由空间。
Vulkan没有内置任何验证层，但是LunarG的Vulkan SDK提供了一组漂亮的检测常见错误的验证层。
它们也完全[开源](https://github.com/KhronosGroup/Vulkan-ValidationLayers)，所以你可以查看哪些验证层用于验证哪些错误或者作出贡献。
使用验证层是避免你的应用因为意外的依赖了一个未知行为而在不同驱动上崩溃的最好解决方案。

验证层只有在安装到系统里以后才能使用。例如，LunarG验证层只有在安装了Vulkan SDK的PC上可用。

以前Vulkan中主要有两类验证层：实例和设备相关的。
这么设计的主要目的是实例验证层只验证类似实例这种全局对象相关的调用，设备相关验证层只验证特定GPU相关的调用。
设备相关的验证层已经废弃，意味着实例验证层适用于所有的Vulkan调用。为了兼容性说明文档也还是推荐你启用设备层相关的验证，有些实现依赖这个选项。
我们仅仅在设备层实例指定相同的验证层，这个我们[接下来](TODO)就会看到。

##### 使用验证层
这节我们将学会如何启用Vulkan SDK提供的标准诊断层。如扩展一样，验证层需要指定名称启用。
所有标准的验证器捆绑进SDK里一个被人熟知的`VK_LAYER_KHRONOS_validation`验证层里。

我们先来添加两个配置向到程序中来指定将启用哪些验证层并且指定哪些启用哪些禁用。
我选择通过确定程序是否已调试模式编译来指定这个值。`NDEBUG`宏是C++标准的一部分意思是“非调试模式”。

```C++
const int WIDTH = 800;
const int HEIGHT = 600;

const std::vector<const char*> validationLayers = {
    "VK_LAYER_KHRONOS_validation"
};

#ifdef NDEBUG
    const bool enableValidationLayers = false;
#else
    const bool enableValidationLayers = true;
#endif
```

我们添加一个新的名叫`checkValidationLayerSupport`的方法来检测是否所有请求的验证层都可用。
首先使用[`vkEnumerateInstanceLayerProperties`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkEnumerateInstanceLayerProperties.html)方法列出所有可用的验证层。
它的使用方法和我们在实例创建章节讲过的[`vkEnumerateInstanceExtensionProperties`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkEnumerateInstanceExtensionProperties.html)方法相同。

```C++
bool checkValidationLayerSupport() {
    uint32_t layerCount;
    vkEnumerateInstanceLayerProperties(&layerCount, nullptr);

    std::vector<VkLayerProperties> availableLayers(layerCount);
    vkEnumerateInstanceLayerProperties(&layerCount, availableLayers.data());

    return false;
}
```

接下来检测是否所有在`validationLayers`里的验证层都在`availableLayers`列表里。
为了使用`strcmp`你需要引入`<cstring>`头文件。

```C++
for (const char* layerName : validationLayers) {
    bool layerFound = false;

    for (const auto& layerProperties : availableLayers) {
        if (strcmp(layerName, layerProperties.layerName) == 0) {
            layerFound = true;
            break;
        }
    }

    if (!layerFound) {
        return false;
    }
}

return true;
```

现在我们可以在`createInstance`方法里使用这个方法了：

```C++
void createInstance() {
    if (enableValidationLayers && !checkValidationLayerSupport()) {
        throw std::runtime_error("validation layers requested, but not available!");
    }

    ...
}
```

现在以调试模式运行程序并确保没有错误产生。如果有，查阅FAQ解决。
最后，如果验证层被启用，修改[`VkInstanceCreateInfo`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/VkInstanceCreateInfo.html)结构体实例化来包含这些验证层名称。

```C++
if (enableValidationLayers) {
    createInfo.enabledLayerCount = static_cast<uint32_t>(validationLayers.size());
    createInfo.ppEnabledLayerNames = validationLayers.data();
} else {
    createInfo.enabledLayerCount = 0;
}
```

如果检测成功，那[`vkCreateInstance`](https://www.khronos.org/registry/vulkan/specs/1.0/man/html/vkCreateInstance.html)应该不会返回一个`VK_ERROR_LAYER_NOT_PRESENT`错误，但你还是要运行程序来看看。

##### 消息回调
验证层默认会把调试信息打印到标准输出里，但我们也可以通过在我们程序里显式指定一个回调函数来捕获。
这也允许你决定哪些

##### 调试实例创建于销毁


##### 配置