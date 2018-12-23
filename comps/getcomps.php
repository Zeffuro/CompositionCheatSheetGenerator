<?php
$files = array();

foreach(array_diff(scandir("./"), array('..', '.', "getcomps.php")) as $file){
	$name = str_replace(".json", "", $file);
	array_push($files, $name);
}

echo(json_encode($files));
