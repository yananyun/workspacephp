



//添加事件函数
//obj 相当于window type相当于onload fn 相当于 function(){}
function addEventOld(obj,type,fn)
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
		//这里的this 对应赋值 看见没有 的写法就是传统的onlclick事件绑定
		fn.call(this);
	}
	
}

//事件切换器
addEvent(window,'load',function(){
	
	var box = document.getElementById('box');
	addEvent(box,'click',toBlue);
	//这里是不能把box的this 传递给 toBlue 用onclick传统的事件绑定会自动传 这里需要更改addEvent代码
	
	
	//这里点击很多次后 too much recursion 太多的递归 
	//因为积累了太多的事件
	//用完的事件 尽快移除
	
	
})

//移除事件函数
function removeEvent(obj,type)
{
	if(obj['on'+type]) obj['on'+type]=null;
	//这样是一刀切的删除   如果要删除事件指定的函数 需要递归遍历删除
	//如何遍历事件名  怎么搞 ？？？
}

function toRed()
{
	this.className='red';
	removeEvent(this,'click');
	addEvent(this,'click',toBlue);
	
}
function toBlue()
{
	this.className='blue';
	removeEvent(this,'click');
	addEvent(this,'click',toRed);

}












