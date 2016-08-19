//冒泡  
//DOM2级事件 定义了两个方法 用于添加事件和删除事件处理程序操作
//addEventListener()和 removeEventListenre() 所有的DOM节点都包含这两个方法
//并且它们都接受3个参数 事件名 函数 冒泡或捕获的布尔值 true 表示捕获 false 表示 冒泡


window.addEventListener('load',function(){
	alert('哪里都有')
});
window.addEventListener('load',function(){
	alert('哪里都有2')
});
window.addEventListener('load',function(){
	alert('哪里都有3')
});
//解决了 方法 覆盖的问题

//解决了多次添加相同的方法引用的问题
function init()
{
	alert('方法重复');
	}
window.addEventListener('load',init);
window.addEventListener('load',init);

//是否传递this  解决 通过this传递 解决事件切换
window.addEventListener('load',function()
		{
	var box =document.getElementById('box');
//	box.addEventListener('click',function(){
//		alert(this);
//	});
	box.addEventListener('click',function(){
		alert('我不要干扰别人 把别的事件删除 别的事件来回切换不要影响到我的存在');
	});
	box.addEventListener('click',toBlue);
		});



function toRed()
{
	this.className='red';
	this.removeEventListener('click',toRed);
	this.addEventListener('click',toBlue);
	}

function toBlue()
{
	this.className='blue';
	this.removeEventListener('click',toBlue);
	this.addEventListener('click',toRed);	
}

window.addEventListener('load',function()
		{
	var box =document.getElementById('box');
//	box.addEventListener('click',function(){
//		alert(this);
//	});
	box.addEventListener('click',function(){
		alert('我要使用捕获');
	});
	document.addEventListener('click',function(){
		alert('我捕获 层在我上面的div的事件 我先处理响应了 这就是捕获 而不像冒泡一样 div 执行完之后再抛给我 我有处理函数就执行')
	});
		});
//w3c标准 IE9才完全支持  
//冒泡和捕获方式 w3c 支持
//这里IE8需要 使用IE自己的事件绑定机制 attachEvent() detachEvent()
//区别 IE不支持捕获 只支持冒泡；IE添加事件不能屏蔽重复的函数;IE中this 指向的是window而不是dom对象；
//在传统事件中IE无法接受到event对象 但使用了attachEvent却可以

//IE的attach 第一 是解决了覆盖问题 但是有不同 顺序不同

window.attachEvent('onload',function(){
	alert('我才是第一个添加的啊 怎么我最后显示');
})
window.attachEvent('onload',function(){
	alert('我才是第2个添加的啊 顺序不对');
})
window.attachEvent('onload',function(){
	alert('我才是第3个添加的啊 顺序不对');
})

//相同函数覆盖问题

function init()
{
	alert('不要让我重复 我是全局的啊 应该每次都属于同一个函数地址 碰到我已经添加过 第二次就不要添加我了');
	
	}

window.attachEvent('onload',init);


