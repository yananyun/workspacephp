<?php
class main {
	function index() {
		echo '告诉我 api main class用途<br/>';
		
		$content = file_get_contents ( 'php://input' );
		$param = json_decode ( $content, true );
		$act = $param ['a'];
		$str = $param ['m'];
		$user = new $str ();
		$data = call_user_func_array ( array (
				$user,
				$act 
		), array (
				$param 
		) );
		print_r ( json_encode ( $data ) );
		echo $data;
	}
}

