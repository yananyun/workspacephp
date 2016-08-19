<?php




function table()
{
	echo '<table border=4 width="800" align="center">';
	
	echo '<caption><h1>表名</h1><caption>';
	
	for($i=0;$i<10;$i++)
	{
		echo '<tr>';
		for($j=0;$j<10;$j++)
		{
			echo '<td> '.($i*10+$j).' </td>';
	
		}
		echo '</tr>';
	
	}
	echo '</table>';
}
table();

function table2($row)
{
	echo '<table border=4 width="800" align="center">';

	echo '<caption><h1>表名</h1><caption>';

	for($i=0;$i<$row;$i++)
	{
		echo '<tr>';
		for($j=0;$j<10;$j++)
		{
			echo '<td> '.($i*10+$j).' </td>';

		}
		echo '</tr>';

	}
	echo '</table>';
}

echo "---------</br>";
table2(5);



function table3($row,$tableName)
{
	echo '<table border=4 width="800" align="center">';

	echo '<caption><h1>'.($tableName).'</h1><caption>';

	for($i=0;$i<$row;$i++)
	{
		echo '<tr>';
		for($j=0;$j<10;$j++)
		{
			echo '<td> '.($i*10+$j).' </td>';

		}
		echo '</tr>';

	}
	echo '</table>';
}

echo "---------</br>";
table3(5,'表名222');

//有返回值
echo "----又返回值-----</br>";
function  table4($row,$tableName)
{
	$str='<table border=4 width="800" align="center">';
	
	 $str.='<caption><h1>'.($tableName).'</h1><caption>';
	
	for($i=0;$i<$row;$i++)
	{
		$str.='<tr>';
		for($j=0;$j<10;$j++)
		{
			$str.='<td> '.($i*10+$j).' </td>';
	
		}
		$str.='</tr>';
	
	}
	$str.='</table>';
	
	return  $str;
}


echo table4(4, '有返回值函数');

//函数的调用 和声明 无序


echo '函数的调用 和 声明 是可以无序的 不同于C语言<br/>';
echo sum(10,5);

function sum($x,$y)
{
	
	$sum=0;
	$sum=$x*$x+$y*$y;
	
	return $sum;
}








