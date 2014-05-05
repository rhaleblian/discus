<!doctype html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="/css/media.css" type="text/css">
    <script type="text/javascript" src="retina.js"></script>
<?php

function echo_row($row) {
	# print a row from the disc table.
	$id = $row['id'];
	$name = $row['name'];
	$label = $row['label'];
	$status = $row['status'];
	if ($status > 0) echo "<s>"; 
	echo "<a href=\"?disc_id=", $id, "\"><li><h2>";
	if (strlen($name) && strlen($label) && ($name != $label)) 
		echo $label, " [", $name, "]";
	else if (strlen($label))
		echo $label;
	else
		echo $name;
	echo "</h2></li></a>";
	if ($status > 0) echo "</s>";
}

$disc_id = $_GET['disc_id'];
$extant = $_GET['extant'];
if ($extant == "") {
	$extant = 0;
}

$ini_path = "../../../.config/media.ini";
$ini_array = parse_ini_file($ini_path);
$conn = mysql_connect($ini_array["host"],
                      $ini_array["user"],
                      $ini_array["passwd"]);
$result = mysql_select_db($ini_array["db"]);

if ($disc_id == "") {
    # Available discs.
?>
    <title>Optical Media Catalog</title>
  </head>
<body>
<div id="view">
<header><h1>Discs</h1></header>
<div id="container">
<ul>
<?php
	$sql = "select id, label, name, status from disc";
	if ($extant == 1) {
	   $sql .= " where status = 0";
	}
	$sql .= " order by label";
	$result = mysql_query($sql);
	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		$rows[] = $row;
	}

	$columns=1;
	$i=0;
	for ($column=1; $column<=$columns; $column++) {
		for (; $i<count($rows)*($column/$columns); $i++) {
			echo_row($rows[$i]);
		}
	}
?>
</ul>
</div></div>
<?php
} else {
	# Disc contents.

	$sql  = "select file.* from disc";
	$sql .= " inner join file on disc.id=file.disc_id";
	$sql .= " where disc.id = '" . $disc_id . "'";
	$result = mysql_query($sql);
	$prev_dir = "";
?>
    <title>Optical Media Catalog</title>
  </head>
  <body>
    <div id="view">
<?php
	while ($row = mysql_fetch_assoc($result)) {
		if ($prev_dir != $row['dir']) {
			echo "[", $row['dir'], "]";
    		$prev_dir = $row['dir'];
		}
		echo "<h2>", $row['name'], "</h2>";
	}
    echo "</div>";
}
?>
  </body>
</html>
