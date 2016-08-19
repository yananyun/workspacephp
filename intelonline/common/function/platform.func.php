<?php
function getPlatFormList() {
	$param = array (
			'm' => 'platform',
			'a' => 'getPlatFormList' 
	);
	$result = curlRequest ( $param );
	return $result;
}