
//alert(window.onload);
//一开始没有注册onload 则返回的是null

//如果已经有onload 会输出的是函数代码块
//alert(typeof window.onload); 
//如果没有则返回的是 Object 如果有的话所有浏览器都是返回function



window.onload=function ()
{
 var box = document.getElementById('box');
 box.onclick=function()
 {
	 alert('传统模式绑定1');
	 toBlue();//注意这里使用匿名函数执行某个函数 这里其中的this作用域回到window 这里的this 就代表了window 不是box
	 //怎么解决
	 toBlue.call(this);//this传递问题
 }
//  box.onclick=toBlue;
}

function toRed()
{
	this.className='red';
	this.onclick=toBlue;
	}
function toBlue()
{
	this.className='blue';
	this.onclick=toRed;
	}
//如果一个页面由两个或多个js 因为多人协同开发 会出现第一个程序员的被第二个程序员的覆盖了
if(typeof window.onload)
	{
	var saved=null;//保存上一个事件对象
	saved=window.onload;
	
	}
// saved 就是window.onload ,saved()相当于 window.onload();但是window.onload 不能执行
//所以saved() 就是执行window.onload
window.onload=function ()
{
      if(saved) saved();//执行上一个事件
	  alert('传统模式绑定2');
 
}

//添加事件函数
//obj 相当于window type相当于onload fn 相当于 function(){}
function addEvent(obj,type,fn)
{
	//用于保存上一个事件
	var saved = null;
	//判断事件是否存在
	if(typeof obj['on'+type] =='function')
		{
		saved= obj['on'+type];
		}
	//执行事件
	obj['on'+type]=function()
	{
		if(saved) saved();
		fn();
	}
	
}

addEvent(window,'load',function(){
	
	alert('addEvent');
});

//对象操作可以使用数组操作来完成 window.onload相当于window['onload']
//window['onload']=function ()
//{
//	}







//事件切换器
