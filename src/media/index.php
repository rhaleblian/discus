<!doctype html>
<html lang="en">
<head>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<?php

function echo_row($row) {
	# print a row from the disc table.
	$id = $row['id'];
	$name = $row['name'];
	$label = $row['label'];
	$status = $row['status'];
	if ($status > 0) echo "<s>";
	echo "<a href=\"?disc_id=", $id, "\"><li>";
	if (strlen($name) && strlen($label) && ($name != $label))
		echo $label, " [", $name, "]";
	else if (strlen($label))
		echo $label;
	else
		echo $name;
	echo "</li></a>";
	if ($status > 0) echo "</s>";
}

$disc_id = "";
if (array_key_exists('disc_id', $_GET)) {
    $disc_id = $_GET['disc_id'];
}
$extant = 1;
if (array_key_exists('extant', $_GET)) {
    $extant = $_GET['extant'];
}

$ini_path = "/home1/haleblia/.config/media.ini";
$ini_array = parse_ini_file($ini_path);
$conn = mysql_connect($ini_array["host"],
                      $ini_array["user"],
                      $ini_array["password"]);
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
			echo "<h4>", $row['dir'], "</h4>";
    		$prev_dir = $row['dir'];
		}
		echo "<p>", $row['name'], "</p>";
	}
    echo "</div>";
}
?>
  </body>
</html>
