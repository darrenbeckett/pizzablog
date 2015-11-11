<?php
include $_SERVER['DOCUMENT_ROOT']."/oven/_inc.php";

$a=$_POST['action'];

// BOXES
if ($a=="newbx") {
	$name = ms(str_replace(" ","",strtolower("My Category")));
	$query[0] = "SELECT * FROM `box` WHERE `slug` LIKE '$name%' AND user = '".$user->id."' ORDER BY `slug` DESC LIMIT 1";
	$result = mysql_query($query[0]);
	while ($ob = mysql_fetch_array($result)) {
		$info = $ob;
	}
	$number="";
	if ($info) {
		$number = str_replace($name,"",$info['slug'])+1;
	}
	
	$query[1] = "INSERT INTO `box` SET `user` = '".$user->id."', `name` = 'My Category', `slug` = '".$name.$number."'";
	$result = mysql_query($query[1]);
	$newbx = mysql_insert_id();

	$arr = array("query"=>$query,"id"=>$newbx);
	echo json_encode($arr);
	return;
}
if ($a=="sortbx") {
	$order = str_replace("e","", join(",",$_POST['order']) );
	$query[1] = "UPDATE `user` SET `sort` = '$order' WHERE `id` = '".$user->id."'";
	$result = mysql_query($query[1]);
	
	$arr = array("query"=>$query);
	echo json_encode($arr);
	return;
}
if ($a=="getbx") {
	$bx = str_replace('e','',$_POST['bx']);
	
	if ($bx=="x") {
		$arr = array();
		$arr['name'] = 'Uncategorized';
		$arr['id'] = 'x';
		$query[2] = "SELECT * FROM `pizza` WHERE user = '".$user->id."' AND (parent IS NULL OR parent = '0') AND deleted IS NULL ORDER BY id DESC";
		$result = mysql_query($query[2]);
		while ($ob = mysql_fetch_object($result)) {
			$blob[$ob->id] = $ob;
			$sort = $ob->sort;
		}
	} else {	
		$query[1] = "SELECT * FROM `box` WHERE `id` = '$bx'";
		$result = mysql_query($query[1]);
		while ($ob = mysql_fetch_array($result)) {
			$arr = $ob;
		}
	
		$query[2] = "SELECT * FROM `pizza` WHERE parent = '$bx' AND deleted IS NULL ORDER BY id DESC";
		$result = mysql_query($query[2]);
		while ($ob = mysql_fetch_object($result)) {
			$blob[$ob->id] = $ob;
			$sort = $ob->sort;
		}
	}

	$arr['query'] = $query;
	$arr['blob'] = $blob;

	$array = $blob; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		if ($ob->sort) {
			$first = explode(',',$ob->sort);
			$query[3] = "SELECT * FROM cheese WHERE id = '".$first[0]."'";
			$result2 = mysql_query($query[3]);
			while ($ob2 = mysql_fetch_object($result2)) {
				$blob[$ob2->parent]->topcheese = strip_tags(htmlspecialchars_decode($ob2->text));
				$lastcheese = $ob2->id;
				$lastparent = $ob2->parent;
			}
		} else {
			$query[3] = "SELECT * FROM cheese WHERE parent = '".$ob->id."' LIMIT 1";
			$result2 = mysql_query($query[3]);
			while ($ob2 = mysql_fetch_object($result2)) {
				$blob[$ob2->parent]->topcheese = strip_tags(htmlspecialchars_decode($ob2->text));
				$lastcheese = $ob2->id;
				$lastparent = $ob2->parent;
			}
		}
		$query[4] = "SELECT * FROM meat WHERE parent = '".$lastcheese."'";
		$result2 = mysql_query($query[4]);
		while ($ob2 = mysql_fetch_object($result2)) {
			$blob[$lastparent]->topmeat = $ob2->data;
		}
	}}	
	if ($arr['sort']) $sort = explode(',',$arr['sort']);
	$pizzas = array(); $num = count($sort)+1;

	$array = $sort; if (is_array($array)) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		$pizzas[$num] = $blob[$ob];
		unset($blob[$ob]);
	}}
	$array = $blob; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { $num++;
		$pizzas[$num] = $ob;
	}}
	if ($pizzas) ksort($pizzas);
	$arr['pizzas'] = $pizzas;	
	
	echo json_encode($arr);
	return;
}
if ($a=="getbxslug") {
	$slug = $_POST['slug'];
	$query[1] = "SELECT * FROM `box` WHERE `slug` LIKE '$slug' AND user = '".$user->id."' AND deleted IS NULL";
	$result = mysql_query($query[1]);
	while ($ob = mysql_fetch_array($result)) {
		$arr = $ob;
	}

	$query[2] = "SELECT * FROM `pizza` WHERE parent = '".$arr['id']."' AND deleted IS NULL ORDER BY id DESC";
	$result = mysql_query($query[2]);
	while ($ob = mysql_fetch_object($result)) {
		$blob[$ob->id] = $ob;
	}
	$arr['query'] = $query;
	$arr['blob'] = $blob;

	$array = $blob; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		if ($ob->sort) {
			$first = explode(',',$ob->sort);
			$query[3] = "SELECT * FROM cheese WHERE id = '".$first[0]."'";
			$result2 = mysql_query($query[3]);
			while ($ob2 = mysql_fetch_object($result2)) {
				$blob[$ob2->parent]->topcheese = strip_tags(htmlspecialchars_decode($ob2->text));
				$lastcheese = $ob2->id;
				$lastparent = $ob2->parent;
			}
		} else {
			$query[3] = "SELECT * FROM cheese WHERE parent = '".$ob->id."' LIMIT 1";
			$result2 = mysql_query($query[3]);
			while ($ob2 = mysql_fetch_object($result2)) {
				$blob[$ob2->parent]->topcheese = strip_tags(htmlspecialchars_decode($ob2->text));
				$lastcheese = $ob2->id;
				$lastparent = $ob2->parent;
			}
		}
		$query[4] = "SELECT * FROM meat WHERE parent = '".$lastcheese."'";
		$result2 = mysql_query($query[4]);
		while ($ob2 = mysql_fetch_object($result2)) {
			$blob[$lastparent]->topmeat = $ob2->data;
		}
	}}	

	if ($arr['sort']) $sort = explode(',',$arr['sort']);
	$pizzas = array(); $num = count($sort)+1;
	$array = $sort; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		$pizzas[$num] = $blob[$ob];
		unset($blob[$ob]);
	}}
	$array = $blob; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { $num++;
		$pizzas[$num] = $ob;
	}}
	if ($pizzas) ksort($pizzas);
	$arr['pizzas'] = $pizzas;
	
	echo json_encode($arr);
	return;
}
if ($a=="setbx") {
	$bx = $_POST['bx']; $pi = $_POST['pi'];
	$query[1] = "UPDATE `pizza` SET `parent` = '$bx' WHERE `id` = '$pi'";
	$result = mysql_query($query[1]);

	$arr['query'] = $query;

	echo json_encode($arr);
	return;
}



//PIZZAS
if ($a=="newpi") {
	$name = ms(str_replace(" ","",strtolower("New Post")));
	$query[0] = "SELECT * FROM `pizza` WHERE `slug` LIKE '$name%' AND user = '".$user->id."' ORDER BY `slug` DESC LIMIT 1";
	$result = mysql_query($query[0]);
	while ($ob = mysql_fetch_array($result)) {
		$info = $ob;
	}
	$number="";
	if ($info) {
		$number = str_replace($name,"",$info['slug'])+1;
	}
	
	$query[1] = "INSERT INTO `pizza` SET `user` = '".$user->id."', `name` = 'New Post', `slug` = '".$name.$number."', display = '".date("Y-m-d 08:00:00")."'";
	$result = mysql_query($query[1]);
	$newpi = mysql_insert_id();

	$query[2] = "INSERT INTO `cheese` SET `parent` = '$newpi'";
	$result = mysql_query($query[2]);
	$newcheese = mysql_insert_id();

	$query[3] = "INSERT INTO `meat` SET `parent` = '$newcheese'";
	$result = mysql_query($query[3]);
	
	$query[4] = "SELECT count(*) AS count FROM pizza WHERE user = '".$user->id."' AND parent IS NULL AND deleted IS NULL";
	$result = mysql_query($query[4]);
	while ($ob = mysql_fetch_array($result)) {
		$unc = $ob['count'];
	}

	$arr = array("query"=>$query,"id"=>$newpi,"unc"=>$unc);
	echo json_encode($arr);
	return;
}
if ($a=="sortpi") {
	$order = $_POST['order'];
	if (is_array($order)) $order = str_replace("e","", join(",",$order) );
	$query[1] = "UPDATE `box` SET `sort` = '$order' WHERE `id` = '".$_POST['bx']."'";
	$result = mysql_query($query[1]);
	
	$arr = array("query"=>$query);
	echo json_encode($arr);
	return;
}
if ($a=="getpi") {
	$pi = str_replace('e','',$_POST['pi']);
	$query[1] = "SELECT * FROM `pizza` WHERE `id` = '$pi'";
	$result = mysql_query($query[1]);
	while ($ob = mysql_fetch_array($result)) {
		$arr = $ob;
	}

	$query[2] = "SELECT C.*,M.id AS mid,M.data FROM `cheese` C LEFT JOIN `meat` M ON (M.parent = C.id) WHERE C.parent = '$pi' AND C.deleted = '0'";
	$result = mysql_query($query[2]);
	while ($ob = mysql_fetch_object($result)) {
		$blob[$ob->id] = $ob;
	}
	
	$cheese = array();
	if ($arr['sort']) $sort = explode(',',$arr['sort']);
	$array = $sort; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { $num++;
		$cheese[$num] = $blob[$ob];
		unset($blob[$ob]);
	}}
	$array = $blob; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		$cheese[$num] = $ob;
	}}
	
	$query[3] = "SELECT * FROM box WHERE user = '".$user->id."' AND deleted IS NULL ORDER BY name";
	$result = mysql_query($query[3]);
	while ($ob = mysql_fetch_object($result)) {
		$boxes[] = $ob;
	}

	$arr['query'] = $query;
	$arr['blob'] = $blob;
	$arr['cheese'] = $cheese;
	$arr['boxes'] = $boxes;

	echo json_encode($arr);
	return;
}
if ($a=="getpislug") {
	$slug = $_POST['slug'];
	$query[1] = "SELECT * FROM `pizza` WHERE `slug` LIKE '$slug' AND user = '".$user->id."' AND deleted IS NULL";
	$result = mysql_query($query[1]);
	while ($ob = mysql_fetch_array($result)) {
		$arr = $ob;
	}

	$query[2] = "SELECT C.*,M.id AS mid,M.data FROM `cheese` C LEFT JOIN `meat` M ON (M.parent = C.id) WHERE C.parent = '".$arr['id']."' AND C.deleted = '0'";
	$result = mysql_query($query[2]);
	while ($ob = mysql_fetch_object($result)) {
		$blob[$ob->id] = $ob;
	}
	
	$cheese = array();
	if ($arr['sort']) $sort = explode(',',$arr['sort']);
	$array = $sort; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { $num++;
		$cheese[$num] = $blob[$ob];
		unset($blob[$ob]);
	}}
	$array = $blob; if ($array) { reset($array); while (list($index,$ob)=each($array)) { $num++;
		$cheese[$num] = $ob;
	}}
	
	$query[3] = "SELECT * FROM box WHERE user = '".$user->id."' AND deleted IS NULL ORDER BY name";
	$result = mysql_query($query[3]);
	while ($ob = mysql_fetch_object($result)) {
		$boxes[] = $ob;
	}

	$arr['query'] = $query;
	$arr['blob'] = $blob;
	$arr['cheese'] = $cheese;
	$arr['boxes'] = $boxes;

	echo json_encode($arr);
	return;
}
if ($a=="newcheese") {
	$pi = $_POST['parent'];
	$text = ms($_POST['text']);
	$query[1] = "INSERT INTO `cheese` SET `parent` = '$pi', text = '$text'";
	$result = mysql_query($query[1]);
	$newcheese = mysql_insert_id();

	$query[2] = "INSERT INTO `meat` SET `parent` = '$newcheese'";
	$result = mysql_query($query[2]);
	$newmeat = mysql_insert_id();

	$arr = array("query"=>$query,"id"=>$newcheese,"mid"=>$newmeat);
	echo json_encode($arr);
	return;
}
if ($a=="sortcheese") {
	$pi = $_POST['parent'];
	$order = str_replace("li","", join(",",$_POST['order']) );
	$query[1] = "UPDATE `pizza` SET `sort` = '$order' WHERE `id` = '".$pi."'";
	$result = mysql_query($query[1]);
	
	$arr = array("query"=>$query);
	echo json_encode($arr);
	return;
}
if ($a=="remcheese") {
	$id = $_POST['id'];
	$query[1] = "UPDATE `cheese` SET `deleted` = '1' WHERE id = '$id'";
	$result = mysql_query($query[1]);
	
	$arr = array("query"=>$query);
	echo json_encode($arr);
	return;
}
if ($a=="newmeat") {
	$id = $_POST['id'];
	$data = $_POST['data'];
	
	if ($id<1) {
		$id = $user->id;
		$query[1] = "UPDATE `user` SET `image` = '$data' WHERE `id` = '$id'";
		$result = mysql_query($query[1]);
	} else {
		$query[1] = "UPDATE `meat` SET `data` = '".str_replace('../','',$data)."' WHERE `id` = '$id'";
		$result = mysql_query($query[1]);
		
		$query[2] = "SELECT * FROM `meat` WHERE `id` = '$id'";
		$result = mysql_query($query[2]);
		while ($ob = mysql_fetch_array($result)) {
			$meat = $ob['parent'];
		}
	}	
	$arr = array("query"=>$query,"meat"=>$meat);
	echo json_encode($arr);
	return;
}
if ($a=="movemeat") {
	$meat = $_POST['meat'];
	if ($meat) { $num=1; while (list($index,$ob)=each($meat)) { $num++;
		$query[$num] = $ob;
		$result = mysql_query($query[$num]);
	}}

	$arr = array("query"=>$query);
	echo json_encode($arr);
	return;
}
if ($a=="remmeat") {
	$id = $_POST['id'];
	
	if ($id<1) {
		$id = $user->id;
		$query[1] = "UPDATE `user` SET `image` = '' WHERE id = '$id'";
		$result = mysql_query($query[1]);
	} else {
		$query[1] = "UPDATE `meat` SET `data` = '' WHERE id = '$id'";
		$result = mysql_query($query[1]);
	}
	$arr = array("query"=>$query);
	echo json_encode($arr);
	return;
}
if ($a=="updateabout") {
	$id = $user->id;
	$data = ms($_POST['data']);

	$query[1] = "UPDATE `user` SET `about` = '$data' WHERE id = '$id'";
	$result = mysql_query($query[1]);
	
	$arr = array("query"=>$query);
	echo json_encode($arr);
	return;
}
if ($a=="updatetext") {
	$table = $_POST['table'];
	$field = $_POST['field'];
	$data  = str_replace('&amp;','&',$_POST['data']);
	if ($_POST['id']) { $id = $_POST['id']; } else { $id = $user->id; }
	if ($table!="cheese") { $data = strip_tags($data); }
	$data = ms($data); $data = str_replace('&amp;','&',$data);
	
	if ($table=="box"||$table=="pizza") {
		$slug = preg_replace("/[^A-Za-z0-9]/", "", $data);
		$slug = strtolower($slug);
		$query[0] = "SELECT * FROM `$table` WHERE `slug` LIKE '$slug%' AND `id` != '$id' ORDER BY `slug` DESC LIMIT 1";
		$result = mysql_query($query[0]);
		while ($ob = mysql_fetch_array($result)) {
			$info = $ob;
		}
		$number="";
		if ($info) {
			$number = str_replace($name,"",$info['slug'])+1;
		}
		$slug = $slug.$number;
		$query[1] = "UPDATE `$table` SET `$field` = '$data', `slug` = '$slug' WHERE id = '$id'";
	} else {
		$query[1] = "UPDATE `$table` SET `$field` = '$data' WHERE id = '$id'";
	}
	$result = mysql_query($query[1]);
	
	$arr = array("query"=>$query,"slug"=>$slug);
	echo json_encode($arr);
	return;
}
if ($a=="setdate") {
	$id = $_POST['id'];
	$data = $_POST['data'];
	
	$query[1] = "UPDATE `pizza` SET `display` = '$data' WHERE `id` = '$id'";
	$result = mysql_query($query[1]);
	
	$arr = array("query"=>$query);
	echo json_encode($arr);
	return;
}
if ($a=="cookpi") {
	$id = $_POST['id'];
	$pub = $_POST['pub'];
	if ($pub=="on") {
		$pub = "'".date("Y-m-d H:i:s")."'";
		$query[2] = "SELECT id,display FROM pizza WHERE id = '$id'";
		$result = mysql_query($query[2]);
		while ($ob = mysql_fetch_array($result)) {
			$display = $ob['display'];
		}
	} else {
		$pub = "NULL";
		$display = '';
	}
	
	$query[1] = "UPDATE pizza SET published = $pub WHERE id = '$id'";
	$result = mysql_query($query[1]);
	
	$arr = array("query"=>$query,"display"=>$display);
	echo json_encode($arr);
	return;
}
if ($a=="dateformat") {
	$id = $user->id;
	$format = $_POST['format'];
	$query[1] = "UPDATE user SET date_format ='$format' WHERE id = '$id'";
	$result = mysql_query($query[1]);
	
	$arr = array("query"=>$query);
	echo json_encode($arr);
	return;	
}
if ($a=="deletepi") {
	$id = $_POST['id'];
	$query[1] = "UPDATE pizza SET deleted = '".date("Y-m-d H:i:s")."' WHERE id = '$id'";
	$result = mysql_query($query[1]);
	
	$query[2] = "SELECT P.*,B.slug AS parentslug FROM pizza P LEFT JOIN box B ON (B.id = P.parent) WHERE P.id = '$id'";
	$result = mysql_query($query[2]);
	while ($ob = mysql_fetch_object($result)) {
		$slug = $ob->parentslug;
	}

	$arr = array("query"=>$query,"next"=>$slug);
	echo json_encode($arr);
	return;		
}
if ($a=="deletebx") {
	$id = $_POST['id'];
	$query[1] = "UPDATE pizza SET parent = NULL WHERE parent = '$id'";
	$result = mysql_query($query[1]);
	
	$query[2] = "UPDATE box SET deleted = '".date('Y-m-d H:i:s')."' WHERE id = '$id'";
	$result = mysql_query($query[2]);

	$arr = array("query"=>$query);
	echo json_encode($arr);
	return;		
}



//SET CATEGORY COUNTS
if ($a=="resetnum") {
	$query = "SELECT * FROM pizza WHERE user = '".$user->id."' AND deleted IS NULL";
	$result = mysql_query($query);
	while ($ob = mysql_fetch_object($result)) {
		if (!$ob->parent)
			$count['X']['active']++;
		elseif ($ob->display<date("Y-m-d H:i:s") && $ob->display>'0000-00-00 00:00:00' && $ob->published)
			$count[$ob->parent]['active']++;
		else
			$count[$ob->parent]['inactive']++;
	}

	$arr = array("count"=>$count);
	echo json_encode($arr);
	return;		
}


// STATIC PAGES
if ($a=="rebuild_all") {
	//update the menu
	//rebuild all category pages
	//rebuild all pages underneath
	
	$return = '';
	$query = "SELECT * FROM box WHERE user = '".$user->id."'";
	$result = mysql_query($query);
	while ($ob = mysql_fetch_object($result)) {
		$return.= make_box($ob->id);
	}
	$static = make_static();

	$arr = array("return"=>$return,"static"=>$static);
	echo json_encode($arr);
	return;		
}
if ($a=="rebuild_menu") {
	//update the menu & header/title only (categories and pages don't change)
	$static = make_static();

	$arr = array("static"=>$static);
	echo json_encode($arr);
	return;		
}
if ($a=="rebuild_box") {
	$return = make_box($_POST['id']);
	$static = make_static();

	$arr = array("return"=>$return,"static"=>$static);
	echo json_encode($arr);
	return;		
}
if ($a=="rebuild_pizza") {
	$return = make_pizza($_POST['id']);
	$static = make_static();

	$arr = array("return"=>$return,"static"=>$static);
	echo json_encode($arr);
	return;	
}

?>