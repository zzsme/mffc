# mffc

##大致说一下设计视图装载器的基本思路：

1. 这个视图装载器类模仿了 Laravel 的 View 类，它实现了一个静态方法  make ，接受视图名称作为参数，以  .  作为目录的间隔符。


2. make 静态方法会检查视图名称是否为空，检查视图文件是否存在，并给出相应的异常。这就是我们引入异常处理包的原因。


3. 视图名称合法且文件存在时，实例化一个 View 类的对象，返回。

4. 使用  with('key', $value)  或者优雅的  withKey($value)  来给这个 View 对象插入要在视图里调用的变量。 withFuckMe($value)  将采用蛇形命名法被转化成  $fuck_me  供视图使用。

5. 最终组装好的 View 对象会被赋给  HomeController  的成员变量  $view ，这个变量是从  BaseController  中继承得来。

6. 父类  BaseController  中的析构函数  __destruct()  将在  function home()  执行完成后处理这个成员变量： extract  出视图要用到的变量， require  视图文件，将最终运算结果发送给浏览器，流程结束。



