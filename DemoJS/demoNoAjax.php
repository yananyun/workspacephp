
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>使用ajax与不使用ajax的区别</title>
</head>
<body>
<script type="text/javascript">

	addEvent(document,'click',function(){

alert("<?php echo date('Y-m-d h:i:s')?>")


		});
	//这里是普通的javascript打印出时间的值 
	//没有用ajax 这里不是缓存不缓存 这必须刷新 时间才会更新


</script>
</body>
</html>