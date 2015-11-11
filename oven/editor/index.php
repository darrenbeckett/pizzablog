<?php
include $_SERVER['DOCUMENT_ROOT']."/oven/_inc.php";

session_start();
if (!$_SESSION['u'.$user->id]->login) {
	header('Location: ../login/');
	exit;
}
error_reporting(E_ALL); ini_set('display_errors', '1');

$sort = array();
$query = "SELECT * FROM user WHERE id = '".$user->id."'";
$result = mysql_query($query);
while ($ob = mysql_fetch_object($result)) {
	$user = $ob;
}
if ($user->sort) $sort = explode(',',$user->sort);

$newest='';
$query = "SELECT * FROM pizza WHERE user = '".$user->id."' AND published IS NOT NULL AND published > '0000-00-00 00:00:00' AND display < '".date("Y-m-d H:i:s")."' AND deleted IS NULL ORDER BY display DESC LIMIT 1";
$result = mysql_query($query);
while ($ob = mysql_fetch_object($result)) {
	$newest = $ob->id;
}

$box = array();
$query = "SELECT * FROM box WHERE user = '".$user->id."' AND (deleted IS NULL OR deleted > '".date("Y-m-d H:i:s")."') ORDER BY id ASC";
$result = mysql_query($query);
while ($ob = mysql_fetch_object($result)) {
	$ob->active=0; $ob->inactive=0;
	$box[$ob->id] = $ob;
}
$uncount=0;
$query = "SELECT * FROM pizza WHERE user = '".$user->id."' AND deleted IS NULL";
$result = mysql_query($query);
while ($ob = mysql_fetch_object($result)) {
	if (!array_key_exists($ob->parent,$box)) 
		$uncount++;
	elseif ($ob->display<date("Y-m-d H:i:s") && $ob->display>'0000-00-00 00:00:00' && $ob->published)
		$box[$ob->parent]->active++;
	else
		$box[$ob->parent]->inactive++;
}

$box[0]->id = "0";
$box[0]->name = "About";
$box[0]->active=0;
$box[0]->inactive=0;
if (str_replace("&lt;br&gt;","",$user->about) || $user->image) {
	$box[0]->published = "1971-09-01 00:00:00";
} else {
	$box[0]->published = NULL;
}
$box[0]->display = "1971-09-01 00:00:00";
if ($box[0]->published && !$newest) $newest="0";



$list = array(); $num=0;
$array = $sort; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
	$list[str_pad($num,10,"0",STR_PAD_LEFT).str_pad($box[$ob]->id,10,"0",STR_PAD_LEFT)] = $box[$ob];
	unset($box[$ob]);
}}
$array = $box; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
	$list[str_pad($num,10,"0",STR_PAD_LEFT).str_pad($ob->id, 10, "0", STR_PAD_LEFT)] = $ob;
}}

$info = array('name'=>'','slug'=>'','text'=>'','display'=>'','published'=>'');
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
<title>PizzaBlog Editor</title>

<script>var preview_lines = '<?=$preview_lines?>';</script>

<script src="/oven/js/jquery.min.js"></script>
<script src="/oven/js/jquery-ui.min.js"></script>
<script src="/oven/js/jquery.ellipsis.min.js"></script>
<script src="/oven/js/switchery.js"></script>
<script src="/oven/js/droparea.js"></script>
<script src='/oven/js/editor.js'></script>
<script src="/oven/js/pizza_events.js"></script>
<script src="/oven/js/pizza_functions.js"></script>
<script src="/oven/js/pizza_oven.js"></script>

<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,300italic,400italic,600italic" rel="stylesheet" type="text/css"/>
<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" rel="stylesheet" type="text/css"/>
<link href="/oven/css/switchery.css" rel="stylesheet"/>
<link href="/oven/css/editor.css" rel="stylesheet"/>
<link href="/oven/css/pizza.css" rel="stylesheet"/>
<link media="only screen and (max-device-width: 480px)" href="/oven/css/ipizza.css" rel="stylesheet"/>
<link href="/_custom.css" rel="stylesheet"/>
<link media="only screen and (max-device-width: 480px)" href="/_icustom.css" rel="stylesheet"/>

<link href="/oven/favicon.png" rel="shortcut icon"/>

</head>
<body>

<div id="content" class="editor">
	<div id="head">
	<h1 id="user_name" class="editable" contenteditable="true" tabindex="-1"><?=$user->name?></h1>
	<h2 id="user_subtitle" class="editable" contenteditable="true" tabindex="-1"><?=$user->subtitle?></h2>
	
	<div id="nav">
		<h3 id="user_menu" class="editable" contenteditable="true" tabindex="-1"><?=$user->menu?></h3>
		<span class="menudrop">&#xe60b;</span>
		<div id="nav_contents">
		<ul id="boxlist">
		<? $array = $list; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { $num++; ?>
			<? if ($ob->id=="0") { ?>
			<li id="e<?=$ob->id?>"><span><?=strip_tags(htmlspecialchars_decode($ob->name))?></span></li>
			<? } else { ?>
			<li id="e<?=$ob->id?>"><span class="title"><?=strip_tags(htmlspecialchars_decode($ob->name))?></span>
				<span class="tag"><span class="active"><?=$ob->active?></span><span class="inactive"><?=$ob->inactive?></span></span>
			</li>
			<? } ?>
		<? }} ?>
		</ul>
		<ul id="boxlist2">
			<li id="eX" class="disabled"><span>Uncategorized</span>
				<span class="tag"><span class="active"><?=$uncount?></span></span>
			</li>
			<li id="eL" class="disabled"><a href="/"><span>Live Site</span></a></li>
		</ul>
		<span id="newbx">+ New Category</span>
		<span id="newpi">+ New Post</span>
		<? /* SHOW A CALENDAR WITH ENTRY DATES HIGHLIGHTED? */ ?>
		</div>
	</div>
	
	</div>
	<form id="form">
		<h3 id="pizza_name" class="editable" contenteditable="true"></h3>
		<div id="switch"></div>
		<div id="published" class="editable" contenteditable="true" tabindex="-1"></div>
		<div id="displayed" class="editable" contenteditable="true"></div>
		<ul id="cheese"></ul>

		<h3 id="box_name" class="editable" contenteditable="true"></h3>
		<ul id="pizzalist"></ul>

		<input type="hidden" name="id" id="id" value=""/>
		<input type="hidden" name="slug" id="slug" value=""/>
		<input type="hidden" name="sort" id="sort" value=""/>
		<input type="hidden" name="deleted" id="deleted" value=""/>
	</form>
	
	<select id="setbox">
		<option>Set Category...</option>
		<? $array = $list; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { if ($ob->id==0) continue; $num++; ?>
		<option value="<?=$ob->id?>"><?=$ob->name?></option>
		<? }} ?>
	</select>
</div>

<input type="hidden" name="about" id="about" value="<?=$user->about?>"/>
<input type="hidden" name="image" id="image" value="<?=$user->image?>"/>
<input type="hidden" name="format" id="format" value="<?=$user->date_format?"$user->date_format":"l, F j, Y"?>"/>

	
<script>
var newest = '0';//'<?=$newest?>';
if (window.location.hash.substring(1)) {
	getSlug(window.location.hash.substring(1));
} else if (newest) {
	$('#boxlist li').removeClass('on');
	$('#e'+newest).addClass('on');
	getBox('e'+newest);
}
</script>

</body>
</html>