<?php
/*最能发挥计算机特长的一个结构 最擅长根据条件重复操作事务
 * 循环结构 条件判断语句 向回转换语句的组合
 * 第一 while 循环 
 * while语句也需要一个bool判断表达式
 * if(表达式)
 *        只一条语句
 * while(表达式)
 * 	  反复执行一条语句
 * while(表达式)
 * {
 *   反复执行这个循环体
 * }       
 * 第二 do-while循环
 * 第三 for循环
 * 根据循环条件不同 我们有两种类型的循环 
 *  一种是 计数循环  for
 *   另外一种 是条件性循环 while do-while foreach(数组中使用最多)
 * */

    $num = 0;
    while ($num<10){
    	
    	echo "只是执行输出的结果<tr>";
    	echo "这是第 $num 次执行<tr>";
    	 $num++;
    }
    
    echo '<table boder="1" width="800" align="center">';
    //让这个表格有边线 宽度为800 居中
    $i = 0;
    //同样的功能使用嵌套循环实现
    while($i<100){
    	
    	if($i%2==0){
    		$bg="#ffffff";
    	}else 
    	{
    		$bg="#cccccc";
    	}
    	echo '<tr bgcolor='.$bg.'>';
    	$j=0;
    	while($j<10)
    	{
    		echo '<td>'.$j.'</td>';
    		$j++;
    	}
    	$i++;
    	echo '</tr>';
    
    }
    echo '</table>';
    
    ?>
    //javascript 严格区分大小写
    <script>
    
               var ys=null;
    			function lrow(obj)
    			{
    				
    				ys=obj.bgColor;
    				obj.bgColor='red';
    			}
    			function drow(obj)
    			{
    				obj.bgColor=ys;
    			}
    </script>
    
    
    
    
    
    
    
    
    
    
    
    
    