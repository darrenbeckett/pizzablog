<?php
	$data = $_POST['data'];
	$timestamp = strtotime($_POST['data']);
	$format = $_POST['format'];
	if (!$format) $format = 'l, F j, Y';
	if (!$timestamp) { 
		$timestamp = strtotime(date("Y-m-d "));
	} 
	if ($_POST['data']!="now" && !preg_match("/:/ui", $data) && !preg_match("/am/ui", $data) && !preg_match("/pm/ui", $data)) {
		$timestamp = strtotime(date("Y-m-d 08:00:00",$timestamp));
	}
	$db = date("Y-m-d H:i:s",$timestamp);
	$man = date("m/d/Y g:i a",$timestamp);
	$post = date($format,$timestamp);

	$arr = array("ts"=>$timestamp,"db"=>$db,"human"=>$man,"post"=>$post);
	echo json_encode($arr);
	return;
?>