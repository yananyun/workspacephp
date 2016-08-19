<?php

/*
 * for 循环适用于 明确知道循环次数的循环
 * while 循环 适用于 明确知道循环终止条件的bool表达式的循环
 * 
 * for(初始化条件;条件表达式;增量){
 *   循环体
 * }
 * */
  $i = 0;
  do{
  	echo "$i : this id do ----while <br/>";
  	$i++;
  	
  }while (0);
  
  $i = 0;
  while(0)
  {
  	echo "$i : this is while <br/>";
  	$i ++;
  }
  
  for($i=0,$j=10;$i<10&&$j>5;$i++,$j--)
  {
  	echo "这是第  $i 次循环执行的结果<br>";
  }
  
  //当外层循环 循环到第一次的时候 内部循环 总循环一次；当外层循环第二次的时候 内部循环 总循环两次
  for($i=1;$i<=9;$i++)
  {
  	for($j=1;$j<$i;$j++)
  	{
  		echo "$j X $i =".$j*$i."&nbsp&nbsp&nbsp";
  	}
  	echo '<br/>';
  	
  }
  echo '---------------<br/>';
  for($i=9;$i >=1;$i--)
  {
  	for($j=1;$j<$i;$j++)
  	{
  		echo "$j X $i =".$j*$i."&nbsp&nbsp&nbsp";
  	}
  	echo '<br/>';
  	 
  }
  echo '---------------<br/>';
  for($i=9;$i >=1;$i--)
  {
  	for($j=$i;$j>=1;$j--)
  	{
  		echo "$j X $i =".$j*$i."&nbsp&nbsp&nbsp";
  	}
  	echo '<br/>';
  
  }
  
  //循环最好不要嵌套 超过两层 循环与if的嵌套 最好不要超过 4层 乱 啊
  
  
  //几个循环有关的语句  break ; continue; exit; return;