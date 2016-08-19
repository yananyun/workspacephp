

//function box() //若果放在一个匿名函数里 就访问不到了 作用域问题
//	{
//		alert('闭包');
//		
//	}
//
//window.onload=function()
//{
//	function box()
//	{
//		alert('闭包');
//		
//	}
//
//
//}


//事件处理函数执行一个函数的时候（这里是在脚本模型中）通过赋值方式 那么直接将函数名赋值给事件即可
// input.onclick=box即可 不要加括号 加括号就自动执行，没有完成赋值操作
//input.onclick =box();这种写法错误
//window.onload= function()
//{
//	//需要DOM访问的支持
//	var input =document.getElementsByTagName('input')[0];
//	//对象.事件处理函数＝函数名 或者匿名函数
//	input.onclick = function()
//	{
//		alert('脚本模型 匿名函数');
//	}
//	
//
//}
function box2()
{
	alert('input 普通 button 脚本模型 函数名');
}
function box3()
{
	alert('input submit 脚本模型 函数名 onsubmit触发 必须 在form表单中有 type为submit的input元素 并且事件代码块是绑定在form上的');
}
window.onload= function()
{
	//需要DOM访问的支持
	var input =document.getElementsByTagName('input')[2];
	//对象.事件处理函数＝函数名 或者匿名函数
//	input.onsubmit =box2;
	input.onclick =box2;
	var input2 =document.getElementsByTagName('input')[3];
	input2.onclick =box3;
	var form= document.getElementsByTagName('form')[0];
	form.onsubmit=box3;
	
	window.onresize=function()
	{
		alert('窗口缩放');
	}
//	window.onscroll  拖动滚动条触发

}
//onfocus 影响元素 窗口 框架 所有表单对象 当鼠标单击或者将鼠标移动聚焦到窗口或框架时触发 (鼠标移动到 但不一定该元素得到焦点 得到焦点才触发)
// ondbclick 当用户双击对象时触发  影响元素 链接 按钮 表单对象
//ondragdrop 影响元素 窗口 触发 当用户将一个对象拖放到浏览器窗口时触发
//onclick 事件有影响的元素 并不是所有的元素都有  ; 触发原因 和 影响的元素 需要注意
//onmouseout 影响元素 只是链接 当鼠标移出链接时触发
//onmouseover 影响元素 只是链接 当鼠标移到链接时触发
//onselect 影响元素 表单元素 当选择一个表单对像时触发 
//onselect        input/textarea 不是click 而是鼠标选中文本全部内容后 然后焦点离开该元素才执行
//onchange 影响元素 textarea/input 当元素内容改变并且失去焦点触发
//onsubmit 影响元素 表单 当发送表格到服务器时
//

//w3c中事件对像与IE的区别
//事件处理三部分组成  对象.事件处理函数=函数; 匿名函数做成普通函数进行赋值 写法


















