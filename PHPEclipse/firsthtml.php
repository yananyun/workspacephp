<html>
	<head>
		<title>  这是第一个PHP程序</title>
		<style>
				body{
				background:yellow;
				}
		</style>
	<head>
	<?php
		$a=100; 
	?>
	<body text="<?php echo "red" ?>">
		<script>
			document.write(new Date());
		</script>
		<?php
			for ($i =0;$i<10;$i++)
			echo "$i #######  $a#<br/>"
		?>
			<?php
		
			echo "查看浏览器源文件 看解释后的文件 和 源代码文件的区别<br/>"
		?>
	</body>
</html>