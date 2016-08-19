<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
</head>
<body>
	<script type="text/javascript">
	
	function computer()
	{
		this.cpu;
		this.memroryzise;
		this.price;
		this.open = function open()
		{
			document.write("计算机打开");
		}
		this.close = function close()
		{
			document.write("计算机关闭");
		}
		this.sleep = function sleep()
		{
			document.write("计算机睡眠");
		}
	}
	
	var compu = new computer();
	compu.open();
	
	
	</script>
</body>
</html>