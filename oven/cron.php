<?php 
include_once "_inc.php";

if (isset($_GET['noisy'])) $echo=1;
if ($echo) echo "<pre>"; 

//get list of directories (dir)
$dir = array();
$one = scandir($root."/pages/");
if ($one) { while (list($index,$ob)=each($one)) {
	if ($ob=="." || $ob==".." || !is_dir($root."/pages/".$ob."/")) continue;
	$dir[$ob] = array();
	$two[$ob] = scandir($root."/pages/".$ob."/");
	if ($two[$ob]) { while (list($index2,$ob2)=each($two[$ob])) {
		if ($ob2=="." || $ob2==".." || !is_dir($root."/pages/".$ob."/".$ob2."/")) continue;
		$dir[$ob][$ob2]++;
	}}
}}
//var_dump($dir);

//get a list of active stories (pi) by slug
$query = "SELECT P.*,B.slug AS parentslug FROM pizza P LEFT JOIN box B ON (P.parent = B.id) WHERE P.user = '".$user->id."' AND P.display < '".date("Y-m-d H:i:s")."' AND P.display > '0000-00-00 00:00:00' AND P.published IS NOT NULL AND P.deleted IS NULL";
$result = mysql_query($query);
while ($ob = mysql_fetch_object($result)) {
	$pi[$ob->parentslug][$ob->slug] = $ob->slug;
	$pi2[$ob->slug] = $ob->slug;
	$pi_box[$ob->slug] = $ob->parent;
}
//var_dump($pi);

//get a list of categories (box) by slug
$query = "SELECT * FROM box WHERE user = '".$user->id."' AND deleted IS NULL";
$result = mysql_query($query);
while ($ob = mysql_fetch_object($result)) {
	$box[$ob->slug] = $ob->slug;
	$box_si[$ob->slug] = $ob->id;

	if (isset($_GET['rebuild'])) make_box($ob->id);

}
//var_dump($box);

//if in pi but not in dir, run make_box command on parent
$array1 = $pi; if ($array1) { $num=0; reset($array1); while (list($index1,$ob1)=each($array1)) { $num++;
	$array = $pi[$index1]; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { $num++;
		if ($dir[$index1][$ob]) {
			if ($echo) echo "all good for $ob \n";
		} else {
			if ($echo) echo "make pizza: ".$pi_si[$ob]." (".$ob.") \n";
			make_box($pi_box[$ob]);
		}
	}}
}}

//if in box but not in dir, run make_box command
$array = $box; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { $num++;
	if ($dir[$ob]) {
		if ($echo) echo "all good for $ob \n";
	} else {
		if ($echo) echo "make box: ".$box_si[$ob]." (".$ob.") \n";
		make_box($box_si[$ob]);
	}
}}

//if in dir but not in pi, delete recursively
//if in dir but not in box, delete recursively
$array1 = $dir; if ($array1) { $num=0; reset($array1); while (list($index1,$ob1)=each($array1)) { $num++;

	$array = $dir[$index1]; if ($array) { $num=0; reset($array); while (list($index,$ob)=each($array)) { $num++;

		if (is_array($pi[$index1]) && in_array($index,$pi[$index1])) {
			if ($echo) echo "keep pizza $index1/$index \n";
		} else {
			if ($echo) echo "   remove pizza: $root/pages/$index1/$index \n";
			unlink($root."/pages/$index1/$index/index.php");
			rmdir($root."/pages/$index1/$index/");
		}

	}}

	if (is_array($box) && in_array($index1,$box)) {
		if ($echo) echo "keep box $index1 \n";
	} else {
		if ($echo) echo "   remove box $root/pages/$index1 \n";
		unlink($root."/pages/$index1/index.php");
		rmdir($root."/pages/$index1/");
	}

}}

make_static();

?>