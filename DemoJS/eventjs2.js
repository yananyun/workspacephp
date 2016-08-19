window.onload = function()
{
// document.onclick=box;//这个box（）函数被onclick绑定了 所以box中的this代表document HTMLDocument	
// document.onmousedown=function(evt)
// {
//	 alert(getButton(evt));
//	 if(getButton(evt)==0) alert('左键');
//	 if(getButton(evt)==1) alert('中键');
//	 if(getButton(evt)==2) alert('右键');
// };
	
	
	
	
//	document.onclick=function(evt)
//	{
//		var e= evt||window.event;
////		alert('x是'+e.clientX+'y是'+e.clientY);//点的区域离左上角的位置
////		//这里注意如果页面内容过多 有滚动 发现点击左上角 还是接近于0，0该怎么处理呢 因为这里是可视区 你滚动后就不算滚动区的
////		//页面的相对坐标
////		var y=e.clientY+document.documentElement.scrollTop;
////		var x=e.clientX+document.documentElement.scrollTop;
////		var x=e.clientX+document.body.scrollTop;//谷歌浏览器兼容
////		alert('滚动 页面的坐标：'+x+';'+y);
//		
//		
//		alert(e.screenX+';'+e.screenY);//这里是屏幕坐标  点击区域距计算机屏幕左上角的距离
//		
//	}
	
	
	//修改键
	 //    有时候这里无效 是因为单击＋ctrl或其它键 是浏览器的快捷键 无法正常返回
	//可以尝试修改要绑定的事件对象 比如这里改为 双击事件 safari 有问题
//	document.onclick=function(evt)
//	{
//		alert(getKey(evt));
//	}
//	document.ondbclick=function(evt)
//	{
//		alert(getKey(evt));
//	}
	
	//捕获键
//	document.onkeydown=function(evt)
//	{
//		var e = evt||window.event;
//		alert(e.keyCode);
//		//键码 不区分大小写
//	}
//	document.onkeypress=function(evt)
//	{
//		var e = evt||window.event;
//		alert(e.keyCode);
//		//如果用keypress 返回keyCode 所有字符键返回的都是0 非字符键不反应
//		//但是Chrome 支持keyCode返回正确的值 并且支持大小写 IE也支持
//		//分号键 IE186 火狐 59
//		//如果不同浏览器在返回的键码值上不相同
//		//safari chrome firefox event对象还支持 charCode属性
//		//键码 不区分大小写
//	}
//	document.onkeypress=function(evt)
//	{
//		var e = evt||window.event;
//		alert(e.charCode);
//		//charCode 支持大小写 返回字符编码  但是IE浏览器不支持 opera浏览器不支持
//	}
	
	
//	document.onkeypress=function(evt)
//	{
//		var e = evt||window.event;
//		alert(String.fromCharCode(getCharCode(evt)));
//		//charCode 支持大小写 返回字符编码  但是IE浏览器不支持 opera浏览器不支持
//	}
	
	
	
//	document.onclick=function(evt)
//	{
//		var e = evt ||window.event;
////		alert(e.target.innerHTML);
//		alert(getTarget(evt));
//	}
	
	
	window.onload=function()
	{
		document.onclick=function()
		{
			alert('document');
		};
		document.documentElement.onclick=function()
		{
			alert('html');
		};
		document.body.onclick=function()
		{
			alert('body');
		};
		document.getElementById('box').onclick=function()
		{
			alert('div');
		};
		document.getElementsByTagName('input')[0].onclick=function()
		{
			alert('input');
		};
		
	};
	
 
}
//事件流 冒泡流 类似IOS事件响应机制
//冒泡流 就是 从 input － div－body－html－document－window 这一事件是冒泡
//捕获 就是 从 window － document－html－div－input 这一事件是冒泡
function getTarget(evt)
{
	var e = evt ||window.event;

	return e.target||e.srcElement;
	}
function box(evt,a)
{
	//这里如果该函数绑定到事件处理函数 参数列表 写两个  下边的length还是只有1个 ；该如何把自己的参数传进来？？
	//这里如果该函数是事件处理函数绑定的函数 浏览器会默认传递一个参数过来 该参数就是event对象
	var e=evt||window.event;
	
	alert(arguments.length);
	alert(arguments[0]);//Object MouseEvent  
	//this表示当前作用域下的那个对象
	alert(e);
	alert(this);//这个 如果直接调用 这里的this就代表了 window
	}
//当触发某个事件时 会包含着所有与事件有关的信息
//这个对象 包含导致事件的元素 事件的类型 以及其它与特定事件相关的信息
//事件对象一般称为event对象 这个对象是浏览器通过函数传递过来的
//event对象有个button属性 来区别 是鼠标左键0 还是鼠标右键2 还是鼠标中键 滚轮1
//（这个处理函数不要绑定到onclick 绑定到onmousedown）

//window.event 这个属性 IE是支持的 Chrome也支持 
//chrome也支持W3c 
//尽量以W3C为标准支持
//跨浏览器鼠标按钮
//绑定到onmousedown时 360浏览器会有问题  onmouseup却大家都正常
function getButton(evt)
{
	var e= evt||window.event;
	if(evt){
		return e.button;
	}else if(window.event)
		{
		switch(e.button)
		{
		case 1:
			return 0;//左键
		case 4:
			return 1;//中键
		case 2:
			return 2;//右键
		case 0:
			return 2;//360浏览器兼容
		}
		
		}
	
	}


function getKey(evt)
{
	var e = evt||window.event;
	var keys=[];
	if(e.shiftKey) keys.push('shift');
	if(e.ctrlKey) keys.push('ctrl');
	if(e.altKey) keys.push('alt');
	
	return keys;
	}


function box2(a,b)
{
	alert(arguments.length);
	}

//可视区及屏幕坐标
//可视区  点击区域离页面之间的距离  屏幕坐标 点击的区域离屏幕之间的距离
//clientX clientY screenX screenY

//keydown 按下键盘上的任意一个键 按下立即触发触发
//keyup 弹起任意键，弹起就是按下然后释放触发
//keypress 按下字符键 abc 123 特殊字符 这些触发  shif ctrl alt 不触发
//键码 键盘上任意键
//字符编码  键盘上可以输出的字符的键
//键码 返回的是ASCII码的小写字母对应的  键码只是返回那个键的值 不认识字母大小写
//键码 在字符上和字符编码的ASCII码是一致的
//一般情况下 我们不管非字符编码键  我们处理字符编码

function getCharCode(evt)
{
 var e = evt||window.event;
 if(typeof e.charCode=='number')
	 {
	 return e.charCode;
	 
	 }else
		 {
		 return e.keyCode;
		 }

}


//event 对象的属性 其它
//target












































