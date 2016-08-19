<?php
class Person {
	public  $name="xiao ming";
	function say() {
		echo "I am ".$this->name;
	}
	function run($addr,$age)
	{

		echo "I am".$this->name;
		echo "I at".$addr;
		echo "my age is".$age;
		echo  ",runing";
	}
}
$per =  new  Person();
//$per->say();
//利用反射实现对象调用方法
//$md = new ReflectionMethod(类名，方法名);
//反射方法对象
//反射好处 可以获得 方法的属性 （是否公开 私有 受保护）
//反射方法对象
// $md = new ReflectionMethod("Person","say");
// //让指定的对象调用这个方法 是谁类调用这个方法呢 是per来调用
//  $md->invoke($per);
 
 //利用反射调用带参数的方法执行
 //反射方法对象
 //php语法规则函数方法的名字的大小写是忽略的
 $md = new ReflectionMethod("Person","run");
 //让指定的对象调用这个方法 是谁类调用这个方法呢 是per来调用
 $md->invokeArgs($per, array("北京","26"));
 
