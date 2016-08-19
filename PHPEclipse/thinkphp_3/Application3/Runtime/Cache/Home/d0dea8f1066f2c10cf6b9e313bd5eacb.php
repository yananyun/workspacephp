<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
</head>
<body>
   <script type="text/javascript">
   var a=56;
   var b=++a;
   document.write("初值为56");
   document.write("<br/>++前a是"+a);
   document.write("<br/>++前b是"+b);
   //
   var a=48;
   var b=a++;
   document.write("初值为48");
   document.write("<br/>++在后a是"+a);
   document.write("<br/>++在后b是"+b+"<br/>");
   
   //关系运算符
   var num1 = prompt("请输入第一个数");
   var num2 = prompt("请输入第二个数");
   //默认情况接受的是字符串 该怎么比较 
   //这里需要注意接受的是string类型数据；需要转换数据
   num1 =parseFloat(num1);
   num2 =parseFloat(num2);
   //JSP 中大于号、小于号、单引号   分别为: &gt;   &lt;   &quot;  
   if(num1>num2)
	   {
	   document.write(" num1 &gt num2 "+"<br/>");
	   
	   }else if(num1<num2){
		   document.write("num1 &lt num2 "+"<br/> ");
	   }else
		   {
		   document.write("num1=num2 "+"<br/> ");
		   }
   
   var num3="123";
   var num4=123;
   if(num3==num4)
	   {
	   document.write("<br/>num3==num4");
	   }
   if(num3===num4)
	   {
	   document.write("<br/>num3===num4");
	   }
   //逻辑运算符 &&与 ||或 !非
   //js中表示假的 false NaN undefined null 0 "" 空数组 空对象
   //空数组就是null 空对象就是null
  /*  var test = new objec1()|| new object();
   document.write("<br/>"+test); */
   var test2 = 0 || 0 || "2"||0;
   document.write("<br/>"+test2);
   var a = 100 || 0;
   document.write("<br/>"+a);
   
   
   </script>
</body>
</html>