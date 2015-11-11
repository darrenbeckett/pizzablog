<?php
$preview_lines=3;

if (file_exists(dirname(__FILE__)."/../_config.php")) {
	include_once dirname(__FILE__)."/../_config.php";
} else {
	header('Location: ../setup/');
	exit;
}

$head_html = '<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta itemprop="name" content="<?=$title?>">
<meta itemprop="description" content="<?=$desc?>">
<meta itemprop="image" content="<?=$image?>">
<meta property="og:title" content="<?=$title?>" />
<meta property="og:description" content="<?=$desc?>" />
<meta property="og:type" content="website" />
<meta property="og:image" content="<?=$image?>" />
<meta property="og:url" content="<?=$url?>" />
<meta property="og:site_name" content="http://'.$user->url.'" />
<title>#Blog Name#<?=$title?" : ".$title:""?></title>
<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,300italic,400italic,600italic" rel="stylesheet" type="text/css"/>
<link href="http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300" rel="stylesheet" type="text/css"/>
<link href="/oven/css/pizza.css" rel="stylesheet"/>
<link href="/oven/css/ipizza.css" rel="stylesheet" media="only screen and (max-device-width: 480px)"/>
<link href="/_custom.css" rel="stylesheet"/>
<link href="/_icustom.css" rel="stylesheet" media="only screen and (max-device-width: 480px)"/>
<link href="/favicon.png" rel="shortcut icon"/>
<script>
	var preview_lines = \''.$preview_lines.'\';
	var google_tracking = \''.$google_tracking.'\';
</script>
</head>
<body>
<div id="content">
';

$menu_html = '	<div id="head">
	<h1 id="user_name"><a href="/">#Blog Name#</a></h1>
	<h2 id="user_subtitle">#Blog Subtitle#</h2>
	<div id="nav">
		<h3 id="user_menu">#Menu Name#</h3>
		<span class="menudrop">&#xe60b;</span>
		<div id="nav_contents">
		<ul id="boxlist">
#Menu List#			<li class=""><a href="<?=$editor?>"><span>&nbsp;</span></a></li>
		</ul>
		</div>
	</div>
	</div>
';

$about_html = '	<div id="page">
		<ul id="cheese">
#Page Data#		</ul>
	</div>
';

$page_html = '	<div id="page">
		<h3 id="pizza_name">#Page Name#</h3>
		<div id="published">#Pub Date#</div>
		<ul id="cheese">
#Page Data#		</ul>
	</div>
	<div id="share">
		<a href="https://www.facebook.com/sharer/sharer.php?s=100&p[url]=<?=$url?>&p[images][0]=<?=$image?>&p[title]=<?=$title?>&display=popup" target="_blank" onclick="popupwindow(this.href, \'Facebook\', 640, 325);return false;"><img src="/oven/css/img/facebook_circle.png" alt="Share on Facebook" width="32" height="32" style="width:32px;height:32px;"></a>&nbsp;
		<a href="http://plus.google.com/share?url=<?=$url?>&hl=en-US" target="_blank" onclick="popupwindow(this.href, \'Google\', 512, 365);return false;"><img src="/oven/css/img/google_circle.png" alt="Share on Google+" width="32" height="32" style="width:32px;height:32px;"></a>&nbsp;
		<a href="https://twitter.com/intent/tweet?text=<?=$title?>&url=<?=$url?>&related=" target="_blank" onclick="popupwindow(this.href, \'Twitter\', 550, 365);return false;"><img src="/oven/css/img/twitter_circle.png" alt="Tweet" width="32" height="32" style="width:32px;height:32px;"></a>&nbsp;
		<a href="http://pinterest.com/pin/create/button/?url=<?=$url?>&amp;media=<?=$image?>&amp;description=<?=$desc?>" target="_blank" onclick="popupwindow(this.href, \'Pinterest\', 750, 230);return false;"><img src="/oven/css/img/pinterest_circle.png" alt="Pin It" width="32" height="32" style="width:32px;height:32px;"></a>&nbsp;
	</div>
';

$category_html = '	<div id="page">
		<h3 id="box_name">#Page Name#</h3>
		<ul id="pizzalist">
#Category Data#		</ul>
	</div>
';

$home_html = '	<div id="page">
#Home Data#
	</div>
';

$rss_html = '<?php header("Content-type: application/xml"); ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title>#Blog Name#</title>
<link>http://'.$user->url.'/</link>
<atom:link href="http://'.$user->url.'/feed/" rel="self" type="application/rss+xml" />
<description>
#Blog Subtitle#
</description>
#Items#
</channel>
</rss>
';

$item_html = '<item>
	<title>#Name#</title>
	<link>http://'.$user->url.'/pages/#Slug#</link>
	<guid>http://'.$user->url.'/pages/#Slug#</guid>
	<pubDate>#Date# -0800</pubDate>
	<description>
<? echo "<![CDATA["; ?>
#Blog Loop#
<? echo "]]>\n"; ?>
	</description>
</item>
';

function ms($val) {
	return str_replace("'","\'",stripslashes(htmlentities(trim($val), ENT_QUOTES, 'UTF-8')));
}

function make_box($id) {
	global $user, $category_html, $root;

	//rebuild the category page
	$query[1] = "SELECT B.* ,U.date_format FROM box B LEFT JOIN user U ON (B.user = U.id) WHERE B.id = '$id'";
	$result = mysql_query($query[1]);
	while ($ob = mysql_fetch_object($result)) {
		$box = $ob;
	}
	if (!$box->date_format) $box->date_format = 'l, F j, Y';
	$query[2] = "SELECT * FROM pizza WHERE parent = '$id' AND published IS NOT NULL AND published > '0000-00-00 00:00:00' AND display < '".date("Y-m-d H:i:s")."' AND deleted IS NULL ORDER BY id DESC";
	$result = mysql_query($query[2]);
	while ($ob = mysql_fetch_object($result)) {
		$pizza[$ob->id] = $ob;
	}
	$array = $pizza; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		if ($ob->sort) {		
			$first = explode(',',$ob->sort);
			$query[3] = "SELECT * FROM cheese WHERE id = '".$first[0]."'";
			$result2 = mysql_query($query[3]);
			while ($ob2 = mysql_fetch_object($result2)) {
				$pizza[$ob2->parent]->topcheese = strip_tags(htmlspecialchars_decode($ob2->text));
				$lastcheese = $ob2->id;
				$lastparent = $ob2->parent;
			}
		} else {
			$query[3] = "SELECT * FROM cheese WHERE parent = '".$ob->id."' LIMIT 1";
			$result2 = mysql_query($query[3]);
			while ($ob2 = mysql_fetch_object($result2)) {
				$pizza[$ob2->parent]->topcheese = strip_tags(htmlspecialchars_decode($ob2->text));
				$lastcheese = $ob2->id;
				$lastparent = $ob2->parent;
			}
		}
		$query[4] = "SELECT * FROM meat WHERE parent = '".$lastcheese."'";
		$result2 = mysql_query($query[4]);
		while ($ob2 = mysql_fetch_object($result2)) {
			$pizza[$lastparent]->topmeat = $ob2->data;
		}
		
	}}	
	
	if ($box->sort) $sort = explode(',',$box->sort);
	$list = array(); $num = count($sort)+1;
	$array = $sort; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		$list[str_pad($num,10,"0",STR_PAD_LEFT).str_pad($pizza[$ob]->id,10,"0",STR_PAD_LEFT)] = $pizza[$ob];
		unset($pizza[$ob]);
	}}
	$array = $pizza; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { $num++;
		$list[str_pad($num,10,"0",STR_PAD_LEFT).str_pad($ob->id, 10, "0", STR_PAD_LEFT)] = $ob;
	}}
	if ($list) ksort($list);
	$textblock = '';
	$array = $list; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { if (!$ob->id) continue; $num++;
		
		$textblock.= '<li class="pza" rel="'.$ob->id.'"><a href="/pages/'.$box->slug.'/'.$ob->slug.'/">';
		$textblock.= '<span class="date">'.date($box->date_format,strtotime($ob->display)).'</span>';
		$textblock.= '<span class="title">'.$ob->name.'</span>';
		$textblock.= '<span class="thumb" style="background-image:url(/'.$ob->topmeat.');"></span>';
		$textblock.= '<span class="blob ellipsis3">'.$ob->topcheese.'&nbsp;</span>';
		$textblock.= '</a></li>'."\n";
	}}

	if (file_exists($root."/pages/".$box->slug."/index.php") && ($box->deleted)) {
		//this isn't displayed, so stop here
	} else {
		$editor = '/oven/editor/#'.$box->slug;
		$content1 = '<? $pg="pages/'.$box->slug.'"; $title="'.$box->name.'"; $editor="'.$editor.'"; include_once $_SERVER[\'DOCUMENT_ROOT\']."/pages/_head.php"; ?>'."\n";
		$content1.= '<? include_once $_SERVER[\'DOCUMENT_ROOT\']."/pages/_menu.php"; ?>'."\n";
		$content1.= $category_html."\n";
		$content1.= '<? include_once $_SERVER[\'DOCUMENT_ROOT\']."/oven/_foot.php"; ?>'."\n";
		$content1 = str_replace('#Page Name#',$box->name,$content1);
		$content1 = str_replace('#Category Data#',$textblock,$content1);
		if (!file_exists($root."/pages/".$box->slug)) mkdir($root."/pages/".$box->slug);
		//write content to "slug"
		file_put_contents($root."/pages/".$box->slug."/index.php", $content1);
	}
	$pizza = '';


	//rebuild the pages underneath
	$query[3] = "SELECT * FROM pizza WHERE parent = '$id' AND published IS NOT NULL AND published > '0000-00-00 00:00:00' AND display < '".date("Y-m-d H:i:s")."' AND deleted IS NULL";
	$result = mysql_query($query[3]);
	while ($ob = mysql_fetch_object($result)) {
		$content1.= make_pizza($ob->id);
	}
	
	return $query;
	
}
function make_pizza($id) {
	global $user, $category_html, $page_html, $root;

	$query[1] = "SELECT P.*,B.id AS parent_id,B.slug AS parent_slug,U.date_format FROM pizza P LEFT JOIN box B ON (P.parent = B.id) LEFT JOIN user U ON (P.user = U.id) WHERE P.id = '$id'";
	$result = mysql_query($query[1]);
	while ($ob = mysql_fetch_object($result)) {
		$pizza = $ob;
	}
	if (!$pizza->date_format) $pizza->date_format = 'l, F j, Y';
	$cheese = array();
	$query[2] = "SELECT C.*,M.data FROM cheese C LEFT JOIN meat M ON (C.id = M.parent) WHERE C.parent = '".$id."' AND C.deleted = '0'";
	$result = mysql_query($query[2]);
	while ($ob = mysql_fetch_object($result)) {
		$ob->text = str_replace('&amp;','&',str_replace('&gt;','>',str_replace('&lt;','<',str_replace('&quot;','"',$ob->text))));
		$cheese[$ob->id] = $ob;
	}	
	if ($pizza->sort) $sort = explode(',',$pizza->sort);
	$list = array(); $num=0;
	$array = $sort; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		$list[str_pad($num,10,"0",STR_PAD_LEFT).str_pad($cheese[$ob]->id,10,"0",STR_PAD_LEFT)] = $cheese[$ob];
		unset($cheese[$ob]);
	}}
	$array = $cheese; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		$list[str_pad($num,10,"0",STR_PAD_LEFT).str_pad($ob->id, 10, "0", STR_PAD_LEFT)] = $ob;
	}}
	$textblock = '';
	$array = $list; if ($array) { $im=0;$tb=0; reset($array); while (list($index,$ob)=each($array)) {
		$image = '';
		if ($ob->data) {
			$image = '<div id="drop" class="drop full" style="background: url(/'.$ob->data.');"></div>'."\n";
			if ($im<1) { $firstimage = $ob->data; $im++; }
		}
		$textblock.= '<li class="chz">'.$image.'<div class="text_block">'.$ob->text.'</div></li>'."\n";
		if ($tb<1) { $firstdesc = $ob->text; $tb++; }
	}}

	if (file_exists($root."/pages/".$pizza->parent_slug."/".$pizza->slug."/index.php") && ($pizza->deleted || !$pizza->published || $pizza->display>date("Y-m-d H:i:s"))) {
		//this isn't displayed, so stop here
	} else {
		//we need to make the page
		$url = 'http://'.$user->url.'/pages/'.$pizza->parent_slug.'/'.$pizza->slug;
		$editor = '/oven/editor/#'.$pizza->parent_slug.'/'.$pizza->slug;
		$content1 = '<? $pg="pages/'.$pizza->parent_slug.'/'.$pizza->slug.'"; $title="'.$pizza->name.'"; $desc="'.strip_tags(str_replace('"','&#34;',$firstdesc)).'"; $image="http://'.$user->url.'/'.$firstimage.'"; $url="'.$url.'"; $editor="'.$editor.'"; include_once $_SERVER[\'DOCUMENT_ROOT\']."/pages/_head.php"; ?>'."\n";
		$content1.= '<? include_once $_SERVER[\'DOCUMENT_ROOT\']."/pages/_menu.php"; ?>'."\n";
		$content1.= $page_html."\n";
		$content1.= '<? include_once $_SERVER[\'DOCUMENT_ROOT\']."/oven/_foot.php"; ?>'."\n";
		$content1 = str_replace('#Page Name#',$pizza->name,$content1);
		$content1 = str_replace('#Pub Date#',date($pizza->date_format,strtotime($pizza->display)),$content1);
		$content1 = str_replace('#Page Data#',$textblock,$content1);
		//make directory if necessary
		if (!file_exists($root."/pages/".$pizza->parent_slug."/".$pizza->slug)) mkdir($root."/pages/".$pizza->parent_slug."/".$pizza->slug);
		//write content to "slug"
		file_put_contents($root."/pages/".$pizza->parent_slug."/".$pizza->slug."/index.php", $content1);
	}

	return	$query;

}


/* THIS FUNCTION MAKES THE HEADER, MENU, ABOUT PAGE, AND SETS THE DEFAULT PAGE */
function make_static() {
	global $user,$head_html,$menu_html,$about_html,$home_html,$root;
	/* GET ALL THE BLOG INFO & BLOG POSTS */
	$query[1] = "SELECT * FROM user WHERE id = '".$user->id."'";
	$result = mysql_query($query[1]);
	while ($ob = mysql_fetch_object($result)) {
		$ob->about = str_replace('&amp;','&',str_replace('&gt;','>',str_replace('&lt;','<',str_replace('&quot;','"',$ob->about))));
		$blog = $ob;
	}
	$query[2] = "SELECT * FROM box WHERE user = '".$user->id."' AND deleted IS NULL";
	$result = mysql_query($query[2]);
	while ($ob = mysql_fetch_object($result)) {
		$box[$ob->id] = $ob;
	}
	$query[3] = "SELECT * FROM pizza WHERE user = '".$user->id."' AND deleted IS NULL";
	$result = mysql_query($query[3]);
	while ($ob = mysql_fetch_object($result)) {

		if ($ob->display<date("Y-m-d H:i:s") && $ob->display>'0000-00-00 00:00:00' && $ob->published)
			$box[$ob->parent]->active++;
		else
			$box[$ob->parent]->inactive++;

	}

	/* CREATE HEAD AND MENU PAGES */
	if ($blog->sort) $sort = explode(',',$blog->sort);
	$list = array(); $num=0;
	$array = $sort; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		$list[str_pad($num,10,"0",STR_PAD_LEFT).str_pad($box[$ob]->id,10,"0",STR_PAD_LEFT)] = $box[$ob];
		unset($box[$ob]);
	}}
	$array = $box; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		$list[str_pad($num,10,"0",STR_PAD_LEFT).str_pad($ob->id, 10, "0", STR_PAD_LEFT)] = $ob;
	}}
	$bullets = '';
	$array = $list; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { if (!$ob->slug) continue; $num++;
		$bullets.='			<li class="<?=$pg=="pages/'.$ob->slug.'"?"on":""?>" rel="'.$ob->id.'"><a href="/pages/'.$ob->slug.'/"><span>'.$ob->name.'</span></a><span class="tag"><span class="active">'.($ob->active?$ob->active:'0').'</span></span></li>'."\n";
	}}
	if (str_replace('<br>','',$blog->about) || $blog->image) {
		$bullets.='			<li class="<?=$pg=="about"?"on":""?>"><a href="/about/"><span>About</span></a></li>'."\n";
	}
		$bullets.='			<li class=""><a href="/feed/"><span>RSS Feed</span></a></li>'."\n";
	$content1 = $head_html;
	$content1 = str_replace('#Blog Name#',$blog->name,$content1);
	//write content to _head.php
	file_put_contents($root."/pages/_head.php", $content1);
	$content2 = $menu_html;
	$content2 = str_replace('#Blog Name#',$blog->name,$content2);
	$content2 = str_replace('#Blog Subtitle#',$blog->subtitle,$content2);
	$content2 = str_replace('#Menu Name#',$blog->menu,$content2);
	$content2 = str_replace('#Menu List#',$bullets,$content2);
	//write content to _menu.php
	file_put_contents($root."/pages/_menu.php", $content2);
	
	/* MAKE THE ABOUT PAGE */
	if ($blog->image) {
		$image = '<div id="drop" class="drop full" style="background: url(/'.$blog->image.');"></div>'."\n";
	}
	$about = '<li class="chz">'.$image.'<div class="text_block">'.$blog->about.'</div></li>'."\n";
	if (!str_replace('<br>','',$about) && !$blog->image) {
		if (file_exists($root."/pages/about.php")) unlink($root."/pages/about.php");
	} else {
		$editor = '/oven/editor/';
		$content1 = '<? $pg="about"; $title="About"; $editor="'.$editor.'"; include_once $_SERVER[\'DOCUMENT_ROOT\']."/pages/_head.php"; ?>'."\n";
		$content1.= '<? include_once $_SERVER[\'DOCUMENT_ROOT\']."/pages/_menu.php"; ?>'."\n";
		$content1.= $about_html."\n";
		$content1.= '<? include_once $_SERVER[\'DOCUMENT_ROOT\']."/oven/_foot.php"; ?>'."\n";
		$content1 = str_replace('#Page Data#',$about,$content1);
		//write content to about.html
		file_put_contents($root."/pages/about.php", $content1);
	}
	
	/* MAKE THE DEFAULT PAGE */
	
	//this should be the title and img or title and first four lines of the five most recent stories
	$query = "SELECT P.*,B.slug AS boxslug,U.date_format FROM pizza P LEFT JOIN box B ON (P.parent = B.id) LEFT JOIN user U ON (P.user = U.id) WHERE P.user = '".$user->id."' AND P.published IS NOT NULL AND P.published > '0000-00-00 00:00:00' AND P.display < '".date("Y-m-d H:i:s")."' AND P.deleted IS NULL ORDER BY P.display DESC LIMIT 5";
	$result = mysql_query($query);
	while ($ob = mysql_fetch_object($result)) {
		if (!$ob->date_format) $ob->date_format = 'l, F j, Y';
		$pizza[$ob->id] = $ob;
	}
	$array = $pizza; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		if ($ob->sort) {		
			$first = explode(',',$ob->sort);
			$query = "SELECT C.text,C.parent,M.data FROM cheese C LEFT JOIN meat M ON (M.parent = C.id) WHERE C.id = '".$first[0]."'";
			$result2 = mysql_query($query);
			while ($ob2 = mysql_fetch_object($result2)) {
				$pizza[$ob2->parent]->topcheese = (htmlspecialchars_decode($ob2->text));
				$pizza[$ob2->parent]->topmeat = $ob2->data;
			}
		} else {
			$num=0;
			$query = "SELECT C.text,C.parent,M.data FROM cheese C LEFT JOIN meat M ON (M.parent = C.id) WHERE C.parent = '".$ob->id."'";
			$result2 = mysql_query($query);
			while ($ob2 = mysql_fetch_object($result2)) {
				if ($num>0) continue;
				$pizza[$ob2->parent]->topcheese = (htmlspecialchars_decode($ob2->text));
				$pizza[$ob2->parent]->topmeat = $ob2->data;
				$num++;
			}
		}
	}}

	if (file_exists($root."/pages/index.php")) {
		unlink($root."/pages/index.php");
	}

	$textblock = '';
	$array = $pizza; if ($array) { 
		//echo "remake the home page \n";
		$num=0; reset($array); while (list($index,$ob)=each($array)) { if (!$ob->id) continue; $num++;
		
			$topurl = "/pages/".$ob->boxslug."/".$ob->slug."/";
			if ($ob->topmeat) {
				$class = '';
				$image = '<a href="'.$topurl.'" class="obvl"><div id="drop" class="drop full" style="background: url(/'.$ob->topmeat.');"></div></a>';
			} else {
				$class = 'noimg';
				$image = '';
			}
			
			$textblock.= '<div id="homelist">';
			$textblock.= '<a href="'.$topurl.'" class="obvl"><span class="title">'.$ob->name.'</span></a>';
			$textblock.= '<span class="date">'.date($ob->date_format,strtotime($ob->display)).'</span>';
			$textblock.= '<li class="chz">'.$image.'<div class="text_block '.$class.'">'.$ob->topcheese.'</div></li>';
			$textblock.= '<a href="'.$topurl.'">more...</a></li>';
			$textblock.= '</div>';
		}
		$editor = '/oven/editor/';
		$content1 = '<? $pg="pages/"; $editor="'.$editor.'"; include_once $_SERVER[\'DOCUMENT_ROOT\']."/pages/_head.php"; ?>'."\n";
		$content1.= '<? include_once $_SERVER[\'DOCUMENT_ROOT\']."/pages/_menu.php"; ?>'."\n";
		$content1.= $home_html."\n";
		$content1.= '<? include_once $_SERVER[\'DOCUMENT_ROOT\']."/oven/_foot.php"; ?>'."\n";
		$content1 = str_replace('#Home Data#',$textblock,$content1);
		file_put_contents($root."/pages/index.php", $content1);
	} else {
		//echo "make the home page match the about page \n";
		copy($root.'/pages/about.php',$root.'/pages/index.php');
	}

	$feed = make_rss();
	return 1;

}

function make_rss() {
	global $user, $rss_html, $item_html, $root;

	$query[1] = "SELECT * FROM user WHERE id = '".$user->id."'";
	$result = mysql_query($query[1]);
	while ($ob = mysql_fetch_object($result)) {
		$ob->about = str_replace('&amp;','&',str_replace('&gt;','>',str_replace('&lt;','<',str_replace('&quot;','"',$ob->about))));
		$blog = $ob;
	}
	$query[2] = "SELECT P.*,B.slug AS boxslug FROM pizza P LEFT JOIN box B ON (P.parent = B.id) WHERE P.user = '".$user->id."' AND P.deleted IS NULL AND P.published IS NOT NULL AND P.published > '0000-00-00 00:00:00' AND P.display <= '".date("Y-m-d H:i:s")."' ORDER BY P.display DESC";
	$result = mysql_query($query[2]);
	while ($ob = mysql_fetch_object($result)) {
		$pizza[$ob->id] = $ob;
	}
	
	// for each pizza, grab the cheese in pizza->sort order
	$array_parent = $pizza; if ($array_parent) { $num_parent=0; reset($array_parent); while (list($index_parent,$ob_parent)=each($array_parent)) { $num_parent++;
		$cheese = array();
		$query = "SELECT C.*,M.data FROM cheese C LEFT JOIN meat M ON (C.id = M.parent) WHERE C.parent = '".$ob_parent->id."' AND C.deleted = '0'";
		$result = mysql_query($query);
		while ($ob = mysql_fetch_object($result)) {
			$ob->text = str_replace('&amp;','&',str_replace('&gt;','>',str_replace('&lt;','<',str_replace('&quot;','"',$ob->text))));
			$cheese[$ob->id] = $ob;
		}	
		if ($ob_parent->sort) $sort = explode(',',$ob_parent->sort);
		$list = array(); $num=0;
		$array = $sort; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
			$list[str_pad($num,10,"0",STR_PAD_LEFT).str_pad($cheese[$ob]->id,10,"0",STR_PAD_LEFT)] = $cheese[$ob];
			unset($cheese[$ob]);
		}}
		$array = $cheese; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
			$list[str_pad($num,10,"0",STR_PAD_LEFT).str_pad($ob->id, 10, "0", STR_PAD_LEFT)] = $ob;
		}}
		$textblock = '';
		$array = $list; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { $num++;
			$image = '';
			if ($ob->data) {
				$image = '<img src="http://'.$user->url.'/'.$ob->data.'"/>'."\n";
			}
			$textblock.= $image.'<div class="text_block">'.$ob->text.'</div>'."\n";
		}}

		/* this strips out some weirdness from the WYSIWYG editor... could prolly do better */
		$textblock = str_replace('> <','><',$textblock);
		$textblock = str_replace('<div class="text_block">','<div>',$textblock);
		$textblock = str_replace('<div><br></div>','<div></div>',$textblock);
		$textblock = str_replace('</div><div>','<br>',$textblock);
		$textblock = str_replace('<div><div>','<div>',$textblock);
		$textblock = str_replace('</div></div>','</div>',$textblock);
		$textblock = str_replace('<div></div>','',$textblock);

		
		$content2[$num_parent] = $item_html;
		$content2[$num_parent] = str_replace('#Name#',urlencode($ob_parent->name),$content2[$num_parent]);
		$content2[$num_parent] = str_replace('#Slug#',$ob_parent->boxslug.'/'.$ob_parent->slug,$content2[$num_parent]);
		$content2[$num_parent] = str_replace('#Date#',date("D, d M Y H:i:s",strtotime($ob_parent->display)),$content2[$num_parent]);
		$content2[$num_parent] = str_replace('#Blog Loop#',$textblock,$content2[$num_parent]);
	}}
	if ($content2) $blog_data = join("\n",$content2);

	$content1 = $rss_html;
	$content1 = str_replace('#Blog Name#',urlencode($blog->name),$content1);
	$content1 = str_replace('#Blog Subtitle#',urlencode($blog->subtitle),$content1);
	$content1 = str_replace('#Items#',$blog_data,$content1);
	file_put_contents($root."/feed/index.php", $content1);
}
?>