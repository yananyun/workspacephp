<?php
	
    $a = 23;//全局变量，该变量的生命周期作用域是整个的php文件，都是可见
    function test()
    {
    	global $a;
    	$a = 45;	
    }
    test ();
    echo $a;
    //超全局变量 除了有全局变量的特点外
    $_SERVER['dyn']='丁亚男';
    echo "<pre>";
    echo print_r($_SERVER);
    echo  "<pre>";
    
   