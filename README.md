# mffc
My First Framework based on Composer

>1. 在 MFFC 里，一个 URL 驱动框架做的事情基本是这样的：入口文件 require 控制器，控制器 require 模型，模型和数据库交互得到数据返回给控制器，控制器再 require 视图，把数据填充进视图，返回给访客，流程结束。


##路由配置
Macaw 只有一个文件，去除空行总共也就一百行多一点，通过代码我们能直接看明白它是怎么工作的。下面我简略分析一下：

1. Composer 的自动加载在每次 URL 驱动 MFFC/public/index.php 之后会在内存中维护一个全量命名空间类名到文件名的数组，这样当我们在代码中使用某个类的时候，将自动载入该类所在的文件。
2. 我们在路由文件中载入了 Macaw 类：“use NoahBuscher\Macaw\Macaw;”，接着调用了两次静态方法 ::get()，这个方法是不存在的，将由 MFFC/vendor/codingbean/macaw/Macaw.php 中的 __callstatic() 接管。

3. 这个函数接受两个参数，$method 和 $params，前者是具体的 function 名称，在这里就是 get，后者是这次调用传递的参数，即 Macaw::get('fuck',function(){...}) 中的两个参数。第一个参数是我们想要监听的 URL 值，第二个参数是一个 PHP 闭包，作为回调，代表 URL 匹配成功后我们想要做的事情。

4. __callstatic() 做的事情也很简单，分别将目标URL（即 /fuck）、HTTP方法（即 GET）和回调代码压入 $routes、$methods 和 $callbacks 三个 Macaw 类的静态成员变量（数组）中。

5. 路由文件最后一行的 Macaw::dispatch(); 方法才是真正处理当前 URL 的地方。能直接匹配到的会直接调用回调，不能直接匹配到的将利用正则进行匹配。

>PHP 框架另外的价值：确立开发规范以便于多人协作，使用 ORM、模板引擎 等工具以提高开发效率。


##用ORM进行数据封装

1. 基础准备 、 构建路由 和 设计 MVC ，我们已经得到了一个结构比较完整的 MVC 架构的 PHP 微框架，但是距离一个真正能够上手使用的框架还差一样东西： 数据库封装
我们选择 Laravel 的 illuminate/database 作为我们的 ORM 包。我试用了几个著名的 ORM，发现还是 Laravel 的 Eloquent 好用！让我们开心的 ORM，开了又开！ :-D
在本系列教程里，每一个 Composer 包都要满足以下基本要求：

	原生依赖 Composer 进行管理
	在好用的基础上尽量简单（比如我们那个超简单的路由包）
	尽量新，用上 PHP 的新特性

##大致说一下设计视图装载器的基本思路：

1. 这个视图装载器类模仿了 Laravel 的 View 类，它实现了一个静态方法  make ，接受视图名称作为参数，以  .  作为目录的间隔符。

2. make 静态方法会检查视图名称是否为空，检查视图文件是否存在，并给出相应的异常。这就是我们引入异常处理包的原因。

3. 视图名称合法且文件存在时，实例化一个 View 类的对象，返回。

4. 使用  with('key', $value)  或者优雅的  withKey($value)  来给这个 View 对象插入要在视图里调用的变量。 withFuckMe($value)  将采用蛇形命名法被转化成  $fuck_me  供视图使用。

5. 最终组装好的 View 对象会被赋给  HomeController  的成员变量  $view ，这个变量是从  BaseController  中继承得来。

6. 父类  BaseController  中的析构函数  __destruct()  将在  function home()  执行完成后处理这个成员变量： extract  出视图要用到的变量， require  视图文件，将最终运算结果发送给浏览器，流程结束。

##添加可热插拔组件Mail
>分析
>邮件发送的整体流程想必大家已经轻车熟路了，现在主要叙述一下 Mail 类的设计过程：

1. 邮件发送的核心参数是 '目标地址'，即邮件要发送到的 E-mail 地址，所以我们设计 Mail::to('oo@xx.me') 作为发送的 '触发 API'。
2. 目前我们采用最简单的 'SMTP' 方式发送邮件，文档在 这里。配置文件放置在 'MFFC/config/mail.php' 中，依旧返回一个数组。
3. Mail 类继承了 'Nette\Mail\Message' 类。'Mail::to()' 的时候创建一个 Mail 类的实例（对象）并返回，这时候其实 'BaseController' 中的析构函数中的代码已经会被触发并处理这个对象了。默认的发送人是从配置文件中读取的 'username'。
4. 'Mail::to()' 支持 字符串 或者数组作为参数，可以一次发送一封或多封邮件。
5. 'from()'、'title()' 和 'content()' 方法用于丰富邮件内容。'content()' 方法可以直接传递 HTML 代码。
6. 'from()' 配置不一定都能够成功，部分邮件服务商不支持修改发送人地址。
7. 这个变量全部组装完成后，被赋值给控制器的 '$mail' 成员变量，然后被析构函数处理，邮件被发送，成功后页面代码被发送回客户端，流程结束。



>本文的大部分内容来自 [岁寒](http://lvwenhan.com) ，在学习laravel时找到的比较好的博文，从中受益匪浅，感谢作者