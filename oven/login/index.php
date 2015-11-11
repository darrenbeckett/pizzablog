<?php

if (file_exists(dirname(__FILE__)."/../../_config.php")) {
	include_once dirname(__FILE__)."/../../_config.php";
} else {
	header('Location: ../setup/');
	exit;
}

if ($_POST) {
	$err=0;
	
	$query = "SELECT * FROM `user` WHERE `id` = '".$user->id."' AND `pass` = password('".$_POST['password']."')";
	$result = mysql_query($query);
	while ($ob = mysql_fetch_array($result)) {
		$info = $ob;
	}
	if (!$info['id']) $err+=1;
	
	if ($err) {
		$query = "SELECT * FROM `user` WHERE `id` = '".$user->id."' AND `pass` = 'newpass'";
		$result = mysql_query($query);
		while ($ob = mysql_fetch_array($result)) {
			$info = $ob;
		}
		if ($info['id']) {
			$query = "UPDATE `user` SET `pass` = password('".$_POST['password']."') WHERE `id` = '".$user->id."'";
			$result = mysql_query($query);
			$err=0;
		}
	}

	if (!$err) {
		session_start();
		$_SESSION['u'.$user->id]->login=1;
		header('Location: /oven/');
		exit;
	}

}

?>
<!doctype html>  
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title>PizzaBlog Login</title>

<script src="/oven/js/jquery.min.js"></script>
<script src="/oven/js/jquery-ui.min.js"></script>

<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,300italic,400italic,600italic" rel="stylesheet" type="text/css"/>
<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" rel="stylesheet" type="text/css"/>
<link href="/oven/css/pizza.css" rel="stylesheet"/>
<link media="only screen and (max-device-width: 480px)" href="/oven/css/ipizza.css" rel="stylesheet"/>

<link href="/oven/favicon.png" rel="shortcut icon"/>

</head>
<body>

<div id="setlog_page">

<h1>PizzaBlog Login</h1>
<? if ($err) { ?><p class="err">An error has occured. Please check the required fields, highlighted below:</p><? } ?>

<form name="form" method="post">

<fieldset>
<label>Password
	<input type="password" name="password" value="<?=stripslashes($_POST['password'])?>" class="<?=$err&1?"err":""?>" style="width:250px;" placeholder=""/></label>
</fieldset>

<button>Login</button>

</form>

<p style="margin-top:50px;text-align:left;font-size:12px;">To reset your password, set the `pass` field to "newpass" in the `user` table. Then return to this page and enter a new password.</p>
<p style="text-align:left;font-size:12px;">(Below is the actual SQL query)</p>
<pre>
UPDATE `user` 
	SET `pass` = 'newpass' 
	WHERE `id` = '<?=$user->id?>';
</pre>

</div>

<script>
$('input').on("change",function() {
	$(this).removeClass('err');
});
</script>

</body>
</html>