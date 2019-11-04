### 开发环境
* Windows
    * Vulkan SDK
    * GLFW
    * GLM
    * 配置Visual Studio
* Linux
    * Vulkan SDK
    * GLFW
    * GLM
    * 配置一个makefile项目
* MacOS
    * Vulkan SDK
    * GLFW
    * GLM
    * 配置Xcode
    
这一章我们会教你设置开发Vulkan应用的环境并安装一些有用的库。
除了编译器以外所有我们用到的工具都兼容Windows、Linux和MacOS，但是安装它们的步骤有点不同，所以我们会分别讲解。

#### Windows
   如果你要开发Windows版，那么我假设你使用Visual Studio 2017编译你的代码。
你也可能使用Visual Studio 2013或2015，但步骤可能不太一样。

##### Vulkan SDK
   开发Vulkan应用你需要的最重要的组件就是SDK。
它包含了头文件、标准验证层、调试工具以及一个Vulkan函数加载器。
这个加载器会在运行时在驱动器里查找函数，类似OpenGL的GLEW，如果你对它比较熟悉。

SDK可以由[LunarG站点](https://vulkan.lunarg.com/)中通过页面最下边的按钮下载。
你不需要创建账户，但那样可以给你提供一些对你可能有用的额外的文档。
![Download Vulkan SDK](https://vulkan-tutorial.com/images/vulkan_sdk_download_buttons.png)
进行安装操作时注意下SDK的安装路径。
首先我们需要确定的事情是确定你的图形卡和驱动器能够支持Vulkan。
进入SDK安装目录，打开`Bin`文件夹然后运行`cube.exe`演示。你看到的效果应该如下：
![cube.exe](https://vulkan-tutorial.com/images/cube_demo.png)
如果你得到了一个错误消息，那么你要确认下你的驱动是否最新，包括Vulkan运行时以及你的图形卡是否被支持。
查看[介绍章节](TODO)获取主要供应商的驱动链接。

这个目录里还有一个对于开发很有用的程序。`glslangValidator.exe`和`glslc.exe`程序从人类可读的[GLSL](https://en.wikipedia.org/wiki/OpenGL_Shading_Language)编译成着色器字节码。
我们会在[着色器模块](TODO)章节深入讲解这部分内容。
`Bin`目录还包含了Vulkan加载器和验证层而`Lib`目录包含了类库。

##### GLFW
   前面已经介绍过Vulkan本身是平台不可知的API并且不包含用于显示渲染结果的创建窗口的工具，
为了从Vulkan的跨平台优势中受益而避免使用可怕的Win32，我们使用支持Windows、Linux和MacOS的[GLFW库](http://www.glfw.org/)来创建窗口。
当然还有其他的库可以达到这个目的，比如[SDL](https://www.libsdl.org/)，但GLFW除了仅仅创建窗口以外还抽象了Vulkan里其他一些平台指定的东西。

你可以从其[官方站点](http://www.glfw.org/download.html)上找到最新发布的GLFW。
这个指南里我们选用64位二进制文件，当然你也可以选择以32位模式构建。
如果那样的话确认将你的Vulkan SDK二进制文件链接到`Lib32`而不是`Lib`。
下载完毕后将其解压到方便访问的路径。
我选择根据文档在Visual Studio目录下创建一个`Libraries`文件夹。
不要因为没有`libvc-2017`文件夹而担心，`libvc-2015`这个文件夹也是适用的。
![Libraries directory](https://vulkan-tutorial.com/images/glfw_directory.png)

##### GLM
   和DirectX12不同，Vulkan不包含线性代数库，所以我们需要下载一个。
[GLM](http://glm.g-truc.net/)是一个优秀的库，专门用于配合图形API使用，它在OpenGL中经常被用到。

GLM是个今包含头文件的库，所以直接下载[最新版](https://github.com/g-truc/glm/releases)并保存到便于访问的路径。
现在你应该拥有一个如下类似的目录了:
![Libraries directory](https://vulkan-tutorial.com/images/library_directory.png)

##### 配置Visual Studio
   现在你已经安装了创建一个Visual Studio的Vulkan项目的所有依赖，接下来写点代码来验证所有的东西都正常。

开启Visual Studio然后创建一个新的`Windows Desktop Wizard`项目，输入一个名字然后点击`OK`。
![New Windows Desktop Wizard Project](https://vulkan-tutorial.com/images/vs_new_cpp_project.png)
确认我们选中的应用类型是`Console Application(.exe)`，这样我们会有一个打印调试信息的地方。
勾选上`Empty Project`可以阻止Visual Studio创建额外的模板代码。
![Create Empty Project](https://vulkan-tutorial.com/images/vs_application_settings.png)
点击`OK`创建项目并新增一个C++源文件。你应该已经知道如何实现了，但为了完整性考虑还是包含了这些步骤:
![Add a C++ source file 01](https://vulkan-tutorial.com/images/vs_new_item.png)
![Add a C++ source file 02](https://vulkan-tutorial.com/images/vs_new_source_file.png)
现在向文件中添加如下代码。不要担心现在你看不明白，我们只要保证这个Vulkan应用能编译运行就好。
下一章我们会从头开始。
```c++
#define GLFW_INCLUDE_VULKAN
#include <GLFW/glfw3.h>

#define GLM_FORCE_RADIANS
#define GLM_FORCE_DEPTH_ZERO_TO_ONE
#include <glm/vec4.hpp>
#include <glm/mat4x4.hpp>

#include <iostream>

int main() {
    glfwInit();

    glfwWindowHint(GLFW_CLIENT_API, GLFW_NO_API);
    GLFWwindow* window = glfwCreateWindow(800, 600, "Vulkan window", nullptr, nullptr);

    uint32_t extensionCount = 0;
    vkEnumerateInstanceExtensionProperties(nullptr, &extensionCount, nullptr);

    std::cout << extensionCount << " extensions supported" << std::endl;

    glm::mat4 matrix;
    glm::vec4 vec;
    auto test = matrix * vec;

    while(!glfwWindowShouldClose(window)) {
        glfwPollEvents();
    }

    glfwDestroyWindow(window);

    glfwTerminate();

    return 0;
}
```
现在让我们配置下项目来摆脱这些错误。
打开项目属性对话框，保证`All Configurations`被选中，因为大多数设置可应用于`Debug`和`Release`模式。
![Project Properties dialog](https://vulkan-tutorial.com/images/vs_open_project_properties.png)
![All Configurations](https://vulkan-tutorial.com/images/vs_all_configs.png)
进入`C++ -> General -> Additional Include Directories`然后在下拉框里点击`<Edit...>`。
![Edit in dropdown box](https://vulkan-tutorial.com/images/vs_cpp_general.png)
添加Vulkan、GLFW和GLM头文件：
![Add headers](https://vulkan-tutorial.com/images/vs_include_dirs.png)
接下来打开`Linker -> General`下的库文件夹编辑器:
![Library Editor](https://vulkan-tutorial.com/images/vs_link_settings.png)
添加Vulkan和GLFW对象文件的路径：
![Add Object files location](https://vulkan-tutorial.com/images/vs_link_dirs.png)
进入`Linker -> Input`，点击`Additional Dependencies`下拉框的`<Edit...>`。
![Additional Dependencies](https://vulkan-tutorial.com/images/vs_link_input.png)
输入Vulkan和GLFW对象文件名字:
![names of object files](https://vulkan-tutorial.com/images/vs_dependencies.png)
最后改用支持C++17特性的编译器：
![C++17 supported compiler](https://vulkan-tutorial.com/images/vs_cpp17.png)
现在可以关闭项目属性对话框了。如果你正确完成了所有事，那你应该不会再看到这些高亮显示的错误提示了。

最后，确认你是按照64位模式编译的：
![64bit mode compiling](https://vulkan-tutorial.com/images/vs_build_mode.png)
按`F5`编译运行之后你应该会看到一个命令提示并弹出一个窗口：
![Compile and Run](https://vulkan-tutorial.com/images/vs_test_window.png)
扩展的数量应该非0。恭喜你，接下来你可以[玩转Vulkan](04_Draw_a_triangle.md)了！

#### Linux
   这个引导说明是针对于Ubuntu用户的，但你应该可以自己编译LunarG SDK，并将`apt`命令替换为适用于你的包管理器命令。
你应该已经安装了支持现代C++(4.8以上)的GCC版本。你还需要CMake和make。

##### Vulkan SDK
   开发Vulkan应用你需要的最重要的组件就是SDK。
它包含了头文件、标准验证层、调试工具以及一个Vulkan函数加载器。
这个加载器会在运行时在驱动器里查找函数，类似OpenGL的GLEW，如果你对它比较熟悉。

SDK可以由[LunarG站点](https://vulkan.lunarg.com/)中通过页面最下边的按钮下载。
你不需要创建账户，但那样可以给你提供一些对你可能有用的额外的文档。
![Download Vulkan SDK](https://vulkan-tutorial.com/images/vulkan_sdk_download_buttons.png)
在你下载好的`.tar.gz`压缩包所在目录打开一个终端然后解压它：
```bash
tar -xzf vulkansdk-linux-x86_64-xxx.tar.gz
```
这条命令会解压SDK里所有文件到工作目录的子目录中，这个子目录以SDK版本命名。将这个目录移到便于访问的位置并记录路径。
在包含`build_examples.sh`这种文件的SDK根目录打开终端。

SDK中的样例以及接下来你的程序将会用到的库取决于XCB库。
这是一个用于连接X窗口系统的C库。在Ubuntu上可以通过`libxcb1-dev`包安装它。同样你还需要通用的X开发文件，它们在`xorg-dev`包中。
```bash
sudo apt install libxcb1-dev xorg-dev
```
现在你可以构建SDK里的Vulkan实例了：
```bash 
./build_examples.sh
```
如果编译成功，你应该有一个`./examples/build/vkcube`的可运行文件。在`examples/build`目录运行`./vkcube`，确保你能看到如下弹窗内容:
![vkcube](https://vulkan-tutorial.com/images/cube_demo_nowindow.png)
如果你看到错误消息，确保你的驱动是最新的，包括Vulkan运行时以及你的图形卡是被支持的。访问[介绍章节](TODO)查找主要供应商的驱动链接。

##### GLFW
   前面已经介绍过Vulkan本身是平台不可知的API并且不包含用于显示渲染结果的创建窗口的工具，
为了从Vulkan的跨平台优势中受益而避免使用可怕的Win32，我们使用支持Windows、Linux和MacOS的[GLFW库](http://www.glfw.org/)来创建窗口。
当然还有其他的库可以达到这个目的，比如[SDL](https://www.libsdl.org/)，但GLFW除了仅仅创建窗口以外还抽象了Vulkan里其他一些平台指定的东西。

我们会从源码安装GLFW而不是选择安装包程序。因为近期版本才支持Vulkan。你可以从[官站](http://www.glfw.org/)上找到源码。
将源码解压到便于访问的位置然后从包含了类似`CMakeLists.txt`文件的目录下打开终端。

运行如下命令来生成makefile并编译GLFW:
```bash 
cmake .
make
```
你可能会看到一个`Could NOT find Vulkan`的警告，但你可以安全地忽略这个信息。如果编译成功，接下来你可以运行以下命令安装GLFW到系统中:
```bash 
sudo make install
```

##### GLM
   和DirectX12不同，Vulkan不包含线性代数库，所以我们需要下载一个。
[GLM](http://glm.g-truc.net/)是一个优秀的库，专门用于配合图形API使用，它在OpenGL中经常被用到。

它是一个只包含头文件的库，可以通过`libglm-dev`包安装。
```bash 
sudo apt install libglm-dev
```

##### 设置一个makefile项目
   现在你已经安装好了所有依赖，我们可以设置一个基础的Vulkan makefile项目并写一点代码来验证是否一切正常。

在便于访问位置创建一个目录并命名为类似`VulkanTest`的名称。创建`main.cpp`文件并插入如下代码。
不要担心看不懂，你只需要保证能正常编译和运行Vulkan应用即可。我们会在下一章从头编写。
```c++
#define GLFW_INCLUDE_VULKAN
#include <GLFW/glfw3.h>

#define GLM_FORCE_RADIANS
#define GLM_FORCE_DEPTH_ZERO_TO_ONE
#include <glm/vec4.hpp>
#include <glm/mat4x4.hpp>

#include <iostream>

int main() {
    glfwInit();

    glfwWindowHint(GLFW_CLIENT_API, GLFW_NO_API);
    GLFWwindow* window = glfwCreateWindow(800, 600, "Vulkan window", nullptr, nullptr);

    uint32_t extensionCount = 0;
    vkEnumerateInstanceExtensionProperties(nullptr, &extensionCount, nullptr);

    std::cout << extensionCount << " extensions supported" << std::endl;

    glm::mat4 matrix;
    glm::vec4 vec;
    auto test = matrix * vec;

    while(!glfwWindowShouldClose(window)) {
        glfwPollEvents();
    }

    glfwDestroyWindow(window);

    glfwTerminate();

    return 0;
}
```
接下来，我们创建一个makefile文件来编译和运行这个基础Vulkan代码。
创建一个空的`Makefile`文件。假设你已经有一些makefile的知识了，比如变量和规则是如何工作的。
如果不是这样，你可以通过[这个教程](https://makefiletutorial.com/)快速入门。

我们首先定义若干变量来简化接下来的编写过程。
定义`VULKAN_SDK_PATH`变量并指向LunarG SDK的`x86_64`目录位置，例如：
```makefile 
VULKAN_SDK_PATH = /home/user/VulkanSDK/x.x.x.x/x86_64
```
确保替换`user`为你自己的名字，替换`x.x.x.x`为正确的版本号。
接下来定义`CFLAGS`变量并指定基础的编译标识：
```makefile 
CFLAGS = -std=c++17 -I$(VULKAN_SDK_PATH)/include
```
我们要使用现代C++(`-std=c++17`)，并且我们需要定位LunarG SDK中`vulkan.h`的位置。
类似的，定义链接标识变量`LDFLAGS`:
```makefile
LDFLAGS = -L$(VULKAN_SDK_PATH)/lib `pkg-config --static --libs glfw3` -lvulkan
```
第一个标识指定了我们希望能找到LunarG SDK`x86_64/lib`目录中类似`libvulkan.so`的库文件。
第二个组件调用了`pkg-config`去自动获取所有构建GLFW应用的必要链接标识。
最后，`-lvulkan`链接了LunarG SDK的Vulkan函数加载器。

指定编译`VulkanTest`的规则现在看来很明了了。确保使用tab而不是空格进行缩进。
```makefile
VulkanTest: main.cpp
    g++ $(CFLAGS) -o VulkanTest main.cpp $(LDFLAGS)
```
要验证这个规则是否有效，只需要进入包含`main.cpp`和`Makefile`的目录里运行`make`。
这应该生成一个`VulkanTest`可执行文件。

现在我们介绍另外两个规则，`test`和`clean`。前者用于执行一个可执行文件，后者用于删除一个构建的可执行文件。
```makefile
.PHONY: test clean

test: VulkanTest
    ./VulkanTest

clean:
    rm -f VulkanTest
```
你会发现`make clean`运行良好，而`make test`很可能会运行失败并给出如下错误：
```
./VulkanTest: error while loading shared libraries: libvulkan.so.1: cannot open shared object file: No such file or directory
```
这是由于`libvulkan.so`库没有安装为系统库。为了解决这个问题，用`LD_LIBRARY_PATH`环境变量显示指定库加载路径：
```makefile
test: VulkanTest
    LD_LIBRARY_PATH=$(VULKAN_SDK_PATH)/lib ./VulkanTest
```
程序应该可以运行成功并显示Vulkan扩展数量。当你关闭这个空白窗口时程序应该以成功返回码(`0`)退出。
然后你还需要设置更多的变量。我们将使用Vulkan的验证层，你需要使用`VK_LAYER_PATH`变量指定去哪里加载Vulkan库。
```makefile
test: VulkanTest
    LD_LIBRARY_PATH=$(VULKAN_SDK_PATH)/lib VK_LAYER_PATH=$(VULKAN_SDK_PATH)/etc/vulkan/explicit_layer.d ./VulkanTest
```
你应该已经有了一个组装为如下格式的完整makefile文件：
```makefile
VULKAN_SDK_PATH = /home/user/VulkanSDK/x.x.x.x/x86_64

CFLAGS = -std=c++17 -I$(VULKAN_SDK_PATH)/include
LDFLAGS = -L$(VULKAN_SDK_PATH)/lib `pkg-config --static --libs glfw3` -lvulkan

VulkanTest: main.cpp
    g++ $(CFLAGS) -o VulkanTest main.cpp $(LDFLAGS)

.PHONY: test clean

test: VulkanTest
    LD_LIBRARY_PATH=$(VULKAN_SDK_PATH)/lib VK_LAYER_PATH=$(VULKAN_SDK_PATH)/etc/vulkan/explicit_layer.d ./VulkanTest

clean:
    rm -f VulkanTest
```
现在你可以将这个目录当作你的Vulkan项目的模板了。拷贝出来重命名为类似`HelloTriangle`的名字并删除`main.cpp`里所有代码。

继续之前，我们再深入了解下Vulkan SDK。里面还有另一个对于开发很有用处的程序。
`x86_64/bin/glslangValidator`和`x86_64/bin/glslc`程序用于从人类可读的[GLSK](https://en.wikipedia.org/wiki/OpenGL_Shading_Language)编译为着色器字节码。
在[着色器章节](TODO)我们会深入讲解。

`Doc`目录包含了关于Vulkan SDK的有用信息以及完整的离线版Vulkan规范。其他文件随便看看即可，我们在入门指南里不会用到。

现在你可以[开启真正的冒险](04_Draw_a_triangle.md)了！

#### MacOS
   这个说明假设你使用Xcode和[Homebrew包管理器](https://brew.sh/)。另外，你需要知道你的MacOS版本不小于10.11并且你的设备支持[Metal API](https://en.wikipedia.org/wiki/Metal_(API)#Supported_GPUs)。

##### Vulkan SDK
   开发Vulkan应用你需要的最重要的组件就是SDK。
它包含了头文件、标准验证层、调试工具以及一个Vulkan函数加载器。
这个加载器会在运行时在驱动器里查找函数，类似OpenGL的GLEW，如果你对它比较熟悉。

SDK可以由[LunarG站点](https://vulkan.lunarg.com/)中通过页面最下边的按钮下载。
你不需要创建账户，但那样可以给你提供一些对你可能有用的额外的文档。
![Download Vulkan SDK](https://vulkan-tutorial.com/images/vulkan_sdk_download_buttons.png)
MacOS的SDK版本内部使用的是[MoltenVK](https://moltengl.com/)。由于Vulkan在MacOS上不被原生支持，实际上MoltenVK其实是将Vulkan API调用转为苹果的Metal图形框架调用的抽象层。
这样可以利用苹果Metal框架的调试能力和优良性能。

下载完毕后将内容解压到你自己选中的文件夹即可(记住你待会儿需要在创建Xcode项目时指定这个地址)。在解压的文件夹中的`Applications`目录里应该有一些用于运行SDK示例的可执行文件。
运行`cube`程序你将看到如下所示：
![cube](https://vulkan-tutorial.com/images/cube_demo_mac.png)

##### GLFW
   前面已经介绍过Vulkan本身是平台不可知的API并且不包含用于显示渲染结果的创建窗口的工具，
为了从Vulkan的跨平台优势中受益而避免使用可怕的Win32，我们使用支持Windows、Linux和MacOS的[GLFW库](http://www.glfw.org/)来创建窗口。
当然还有其他的库可以达到这个目的，比如[SDL](https://www.libsdl.org/)，但GLFW除了仅仅创建窗口以外还抽象了Vulkan里其他一些平台指定的东西。

我们使用Homebrew包管理器在MacOS上安装GLFW。Vulkan当前(当前写作时间)的稳定版3.2.1在MacOS上还不能被完整使用，因此我们安装`glfw3`包的最新版本:
```bash 
brew install glfw3 --HEAD
```

##### GLM
   Vulkan不包含线性代数库，所以我们需要下载一个。
[GLM](http://glm.g-truc.net/)是一个优秀的库，专门用于配合图形API使用，它在OpenGL中经常被用到。

它是一个只包含头文件的库，可以通过`glm`包安装。
```bash 
brew install glm
```

##### 配置Xcode
   现在所有依赖已安装完毕我们可以配置一个Vulkan的基础的Xcode项目了。
这里大部分的引导内容看上去都那么地"水到渠成"，所以我们可以将所有依赖链接到项目中。
但是，记住下面提到`vulkansdk`的地方是设置我们解压Vulkan SDK的文件夹路径。

启动Xcode并创建一个Xcode项目。在即将打开的窗口上选择`Application > Command Line Tool`。
![Start Xcode](https://vulkan-tutorial.com/images/xcode_new_project.png)
选择`Next`输入项目名称并指定`Language`为`C++`。
![Create Xcode project](https://vulkan-tutorial.com/images/xcode_new_project_2.png)
点击`Next`项目应该就创建完成了。现在让我们替换自动生成的`main.cpp`文件为如下代码：
```c++
#define GLFW_INCLUDE_VULKAN
#include <GLFW/glfw3.h>

#define GLM_FORCE_RADIANS
#define GLM_FORCE_DEPTH_ZERO_TO_ONE
#include <glm/vec4.hpp>
#include <glm/mat4x4.hpp>

#include <iostream>

int main() {
    glfwInit();

    glfwWindowHint(GLFW_CLIENT_API, GLFW_NO_API);
    GLFWwindow* window = glfwCreateWindow(800, 600, "Vulkan window", nullptr, nullptr);

    uint32_t extensionCount = 0;
    vkEnumerateInstanceExtensionProperties(nullptr, &extensionCount, nullptr);

    std::cout << extensionCount << " extensions supported" << std::endl;

    glm::mat4 matrix;
    glm::vec4 vec;
    auto test = matrix * vec;

    while(!glfwWindowShouldClose(window)) {
        glfwPollEvents();
    }

    glfwDestroyWindow(window);

    glfwTerminate();

    return 0;
}
```
记住目前你不必了解这些代码，只需要完成API调用确保一切可以正常使用即可。

Xcode可能已经提示出一些类似"Library not found"的错误了。接下来我们配置一下项目来解决这些问题。
在`Project Navigator`面板选择你的项目，选择`Build Settings`标签页，然后：
    * 找到**Header Search Paths**字段，添加`/usr/local/include`(这是Homebrew安装头文件的路径，所以glm和glfw3头文件应该在这里)和Vulkan头文件的路径`vulkansdk/macOS/include`。
    * 找到**Library Search Paths**字段，添加`/usr/local/lib`(再次说明，这是Homebrew安装库文件的路径，所以glm和glfw3库文件应该都在这里)和`vulkansdk/macOS/lib`。
它现在看起来应该是这样(显然，路径依你自己放置文件位置不同有所区别)：
![Setting headers and libraries find path](https://vulkan-tutorial.com/images/xcode_paths.png)
现在在`Build Phases`标签页，在**Link Binary With Libraries**我们添加`glfw3`和`vulkan`框架。
简单起见我们在项目里添加动态链接库(如果你想使用静态链接库可以参考库文档解决)。
    * 配置glfw,打开`/usr/local/lib`目录你将看到`libglfw.3.x.dylib`这种文件("x"是库的版本号，它依据你从Homebrew安装的包不同而有所区别)。只要简单地将其拖拽到Xcode的**Linked Frameworks**标签和**Libraries**标签即可。
    * 配置vulkan,进入`vulkansdk/macOS/lib`目录，对`libvulkan.1.dylib`和`libvulkan.1.x.xx.dylib`做同样的事情("x"是你下载的SDK版本号)。
添加这些库文件后在相同的标签页的**Copy Files**更改`Destination`为"Frameworks"，清除子路径并取消选中"Copy only when installing"。点击"+"号添加这些框架。

你的Xcode配置项应该如下所示：
![Xcode configuration](https://vulkan-tutorial.com/images/xcode_frameworks.png)
最后一件事，你可能需要配置若干环境变量。在Xcode工具栏选择`Product > Scheme > Edit Scheme...`在`Arguments`标签页添加以下环境变量：
    * VK_ICD_FILENAMES=vulkansdk/macOS/etc/vulkan/icd.d/MoltenVK_icd.json
    * VK_LAYER_PATH=vulkansdk/macOS/etc/vulkan/explicit_layer.d
它应该看上去这样子：
![env setting](https://vulkan-tutorial.com/images/xcode_variables.png)
最终你完成了所有设置。现在如果运行项目(记住依据你的选择设置调试或发布的构建配置)应该看到如下所示：
![Run Xcode project](https://vulkan-tutorial.com/images/xcode_output.png)
扩展数应该不为0。其他日志为库日志，这些日志依据你的配置有所不同。

现在你可以尝试[真正的事](04_Draw_a_triangle.md)了。