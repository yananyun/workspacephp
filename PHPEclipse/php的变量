<?php
//使用数据的时候 如果该数据使用多次 可以把它声明为变量使用
//功能执行语句
  $a ="demoTest";
 //for循环结构定义语句 $i 括号中是 指令分隔符 分号 ；不是逗号 要注意
	for($i = 0;$i<10;$i++)
	{
		echo "#######<br/>";
	}
	echo $a;
//变量声明 通过赋值符 将值赋值给变量；	变量输出 直接echo 加$变量名；变量调用
	
//变量有关的函数
//判断变量是否存在
   if (isset($a))
   {
   	echo "a存在";
   }else 
   {
   	echo "不存在a";
   }
 //释放变量 
   unset($a);
   if (isset($a))
   {
   	echo "a存在";
   }else
   {
   	echo "不存在a";
   }
 //变量声明前一定要用$符 声明和使用都要使用这个符号
 //不能以数字开头
 //不能使用PHP的运算符号＋－等
 //php竟然可以使用系统关键字做为变量名
  $if = 10000;
  echo $if."<br/>";
 //注意php变量区分大小写 （php只有变量和常量区分大小写 其它的不区分（关键字 函数））
  $b= 10;
  $A ="abc";
  echo $b."<br/>";
  echo $A."<br/>";
 //为了维护的考虑 将变量名命名一定要有意义
 //变量命名的风格 驼峰结构
 
  //可变变量 让人想起 java 或 oc 中的映射机制
  
  //动态变量 $符必须紧挨 可变变量
  
  $one = "a";
  $two ="one";
  $three ="two";
  $four ="three";
  
  echo $$four."<br/>";
  

  //php默认是传值赋值  将一个变量赋值给另外一个变量 改变其中一个变量的值 将会影响另一个值
    $p11="1000";
    $p22 =$p11;
    $p22 ="2000";
    echo $p11."<br/>";
    echo $p22."<br/>";
    
//php引用赋值  将一个变量赋值给另外一个变量 改变其中一个变量的值 将会影响另一个值
    $p11="1000";
    $p22 =&$p11;
    $p22 ="2000";
    echo $p11."<br/>";
    echo $p22."<br/>";
    
//变量的类型  php是弱类型的语言  但是还是要区分类型
//php中有8中原始类型  
/*
 * 四种标量 整型 int integer 布尔型 bool boolen  浮点型 float double real 字符串 string
 * 两种复合类型 上面的变量只能存储一个值  这里的复合类型 可以存储多个值  数组array 对象 object
 * 2种特殊类型 资源类型 resource 空类型 null
 * 
 * */
 //和变量类型有关的函数 Var_dump
 
      $var1 = 10;
      
      echo '<pre>';
      var_dump($var1);
      echo  '<pre>';
      
      
      echo "--------------------------</br>";
      $var1= "test var1";
      
      echo '<pre>';
      var_dump($var1);
      echo  '<pre>';
      
      $var1 = array(1,2,3);
      echo "--------------------------</br>";
      
      
      echo '<pre>';
      var_dump($var1);
      echo  '<pre>';
      $var1 = new mysqli("localhost","root","dyn123456","news");
      echo "--------------------------</br>";
      
     
      echo '<pre>';
      var_dump($var1);
      echo  '<pre>';
  
      $var1 = fopen("1.php","r");
      echo "--------------------------</br>";
      
       
      echo '<pre>';
      var_dump($var1);
      echo  '<pre>';
  
      $var1 = null;
      echo "--------------------------</br>";
      
       
      echo '<pre>';
      var_dump($var1);
      echo  '<pre>';
  
      
      $int =10;
      echo $int."<br/>";
      $int = 045;//以八进制标识要赋的值
      echo $int."<br/>";
      $int = 0xff ;//以十六进制标识
      //整数的最大值 4字节 2的32次方
      echo $int."<br/>";
      
      $float = 10;
      echo $float."<br/>";
      $float = -10;
      echo $float."<br/>";
      $float= 3.14E5;
      echo $float."<br/>";
      $float= 3.14E+2;
      echo $float."<br/>";
      $float= 3.14E-2;
      echo $float."<br/>";
      
      //字符串的声明
      $str = 'aaaaaa';
      $str = "wwwwww"; 
      //C语言种单引号只能用来声明字符 但是php种 单引号与双引号都可以声明字符串，并且字符串没有字符限制；
      //区别 双引号的字符串中 既可以直接解析变量 又可以直接使用转义字符
      //单引号中 不可以解析变量 不可以使用转义字符（单引号中只能转义单引号本身和转义字符"\"）
      //单引号中不能使用单引号
      //双引号中不能使用双引号
      $int = 10;
      $str ="aa  $int  aaaaaa $int sssss $int sss";
      echo $str."<br/>";
      //但是变量名要挨着 不要空格 会发现 找不到变量 解决方法 用大括号将变量包起来
      $str ="aa{$int}aaaaaa{$int}sssss${int}yyyyyy";
      echo $str."<br/>";
      
      //双引号要检查是否要解析变量 所以声明字符串尽量使用单引号 减少系统开销
      
      $str ='aa{$int}aa\'aa\\aa{$int}sss\nss${int}yyyyyy';
      echo $str."<br/>";
      
      //字符串定界符号的声明  文章中 难免会有双引号 也会有单引号 还各种嵌套 所以使用定界符
      //定界符 是自定义的字符串 他后面不能有任何字符 空格也不可以 也要以这个字符串结束，但结束前不能有任何字符 同样空格也不可以
//        $str =<<<hello
//      uwuuwuwuusjsjs""""skksk'''"""uuwiw"""""iiwiiwiiw
// hello;      
//        echo $str."<br/>";

      
      /*
       * php中的bool值为false情况
       * 
       * 
       * 
       * 
       * */      
    $bool = false; //true
    echo $bool."111<br/>";
    $bool =0;//非0的数
    echo $bool."222<br/>";
    $bool =0.000; //有非0的数出现
    echo $bool."33<br/>";
    $bool="";
    echo $bool."44<br/>";
    $bool=" ";
    echo $bool."55<br/>";
    $bool=null;//非空代表
    echo $bool."66<br/>";
    $bool="0";//非空非0的字符串
    echo $bool."77<br/>";
    $bool=array();//有成员的数组
    echo $bool."88<br/>";
       
      
    define("HOME","aaaaaaasssss");
    if(defined("HOME"))
    {
    	echo HOME;
    }else
    {
    	define("HOME","bbbbbbbbbb");
    
    }
    echo HOME;
    
    //系统预定义常量 就是不定义就可以使用 魔术常量 而魔术常量 
    //比如 __FILE__ 输出当前文件名 还有绝对路径
    //__LINE__输出当前是哪一行
    //__FUNCTION__函数中 输出函数名
    //PHP_VERSION 输出php当前的版本
    
    echo __FILE__."<br/>";
    echo __LINE__."<br/>";
    echo __FUNCTION__."<br/>";//要在方法中使用
    echo PHP_VERSION."<br/>";
    
    function demo()
    {
    	echo __FUNCTION__."<br/>";
    }
    demo();
  
  
  
  
   
	