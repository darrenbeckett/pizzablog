<?php

if (file_exists(dirname(__FILE__)."/../../_config.php")) {
	header('Location: ../login/');
	exit;
}

if ($_POST) {
	$err=0;
	while (list($index,$ob)=each($_POST)) {
		$info[$index]=ms($ob);
	}

	//echo "<pre>"; var_dump($info); exit;

	if (!$info['db_host'])  $err+=1;
	if (!$info['db_name'])  $err+=2;
	if (!$info['db_user'])  $err+=4;
	if (!$info['db_pass'])  $err+=8;
	if (!$info['domain'])   $err+=16;
	if (!$info['password']) $err+=256;
	
	$db = mysql_connect($info['db_host'], $info['db_user'], $info['db_pass']);
	if (!$db)  $err+=512;
	$test_database = mysql_select_db($info['db_name'],$db);
	if (!$test_database)    $err+=1024;	

	if (!$err) {
	
		$query = "INSERT INTO `user` SET `name` = '".$info['blogname']."', `pass` = password('".$info['password']."')";
		$result = mysql_query($query);
		$id = mysql_insert_id();
		
		if ($id<1) {
			$err+=2048;
		} else {
			$file = $info['rootdir'].'/_config.php';
			$data = '';
			$data.= '<?php'."\n";
			$data.= '$db = mysql_connect("'.$info['db_host'].'", "'.$info['db_user'].'", "'.$info['db_pass'].'");'."\n";
			$data.= 'mysql_select_db("'.$info['db_name'].'",$db);'."\n";
			$data.= '$user->id  = \''.$id.'\';'."\n";
			$data.= '$user->url = \''.$info['domain'].'\';'."\n";
			$data.= '$root = \''.$info['rootdir'].'\';'."\n";
			$data.= '$google_tracking = \''.$info['tracking'].'\';'."\n";
			$data.= '?>';
			$success = file_put_contents($file, $data);
			if (!$success) $err+=4096;
			else {
				session_start();
				$_SESSION['u'.$id]->login=1;
				header('Location: /oven/');
				exit;
			}

		}
		
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
<title>PizzaBlog Setup</title>

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

<h1>PizzaBlog Setup</h1>
<? if ($err&4096) { ?><p class="err">There was a problem writing to your database, please make sure you've created the database properly.</p>
<? } elseif ($err&2048) { ?><p class="err">There was a problem writing your config file. Please make sure the server has write permissions to the web root.</p>
<? } elseif ($err) { ?><p class="err">An error has occured. Please check the required fields, highlighted below:</p><? } ?>

<form name="form" method="post">

<fieldset><legend>Database Information</legend>
<label>Database Host
	<input type="text" name="db_host" value="<?=$info['db_host']?stripslashes($info['db_host']):"localhost"?>" class="<?=($err&1 || $err&512 || $err&1024)?"err":""?>" placeholder=""/></label>
<label>Database Name
	<input type="text" name="db_name" value="<?=stripslashes($info['db_name'])?>" class="<?=($err&2 || $err&512 || $err&1024)?"err":""?>" placeholder="pizzablog"/></label>
<label>Database User
	<input type="text" name="db_user" value="<?=stripslashes($info['db_user'])?>" class="<?=($err&4 || $err&512 || $err&1024)?"err":""?>" placeholder="pizzablog_user"/></label>
<label>Database Password
	<input type="text" name="db_pass" value="<?=stripslashes($info['db_pass'])?>" class="<?=($err&8 || $err&512 || $err&1024)?"err":""?>" placeholder="pizzablog_password"/></label>
</fieldset>

<fieldset><legend>Site Information</legend>
<label>Domain (without the http://)
	<input type="text" name="domain" value="<?=stripslashes($info['domain'])?>" class="<?=$err&16?"err":""?>" style="width:250px;" placeholder="domain.com"/></label>
<!--<label>Root Directory-->
	<input type="hidden" name="rootdir" value="<?=$info['rootdir']?stripslashes($info['rootdir']):$_SERVER['DOCUMENT_ROOT']?>"/><!-- class="<?=$err&32?"err":""?>" style="width:350px;" placeholder=""/></label>-->
<label>Blog Name (optional)
	<input type="text" name="blogname" value="<?=stripslashes($info['blogname'])?>" class="<?=$err&64?"err":""?>" style="width:250px;" placeholder=""/></label>
<label>Google Tracking Code (optional)
	<input type="text" name="tracking" value="<?=stripslashes($info['tracking'])?>" class="<?=$err&128?"err":""?>" placeholder="UA-12345678-1"/></label>
</fieldset>

<fieldset><legend>Admin Information</legend>
<label>Password
	<input type="text" name="password" value="<?=stripslashes($info['password'])?>" class="<?=$err&256?"err":""?>" placeholder=""/></label>
<p>Be sure to memorize or store this password in a safe place.</p>
</fieldset>

<button>Save</button>

</form>

</div>

<script>
$('input').on("change",function() {
	$(this).removeClass('err');
});
</script>

</body>
</html>

<?
function ms($val) {
	return str_replace("'","\'",stripslashes(htmlentities(trim($val), ENT_QUOTES, 'UTF-8')));
}
?>