<?php
   
include "smarty.class.php";

$smarty = new Smarty;

$title = "this is article title";
$content = "this is a article content";

// $smarty -> title =$title;
// $smarty -> content = $content;

$smarty -> assign('title',$title);
$smarty -> assign('content',$content);

$smarty -> display('smarty.html');
var_dump($smarty);