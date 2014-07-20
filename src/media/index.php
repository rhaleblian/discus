<!doctype html>
<html lang="en">
<head>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
<!--<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">-->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<title>Optical Media Catalog</title>
</head>
<body class="body">
<div class="container">
<?php

function connect() {
    $ini_path = "/home1/haleblia/.config/media.ini";
    $ini_array = parse_ini_file($ini_path);
    $conn = mysql_connect($ini_array["host"],
                          $ini_array["user"],
                          $ini_array["passwd"]);
    $result = mysql_select_db($ini_array["db"]);
    return $conn;
}

function status_string($code) {
    if ($code == 0) { return "extant"; }
    else { return "non-extant"; }
}

function disc_row($row) {
	# return HTML for a row from the disc table.
	$id = $row['id'];
	$name = $row['name'];
	$label = $row['label'];
	$status = $row['status'];
	$html = '<tr>';
	$html .= '<td><a href="?disc_id=' . $id . '">' . $label . '</a></td>';
	$html .= "<td>" . $name . "</td><td>" . status_string($status) . "</td>";
	$html .= "</tr>";
    return $html;
}

function search($term) {
    echo "<p>Search: ", $term, "</p>";
    echo '<table class="table">';
    echo '<thead><tr><td>disc</td><td>file</td><td>folder</td></tr></thead>';
    echo '<tbody>';
    $sql = "SELECT * FROM file_view WHERE name LIKE '" . $term . "' OR dir LIKE '" . $term . "';";
    $result = mysql_query($sql);
    $rows = array();
	while ($row = mysql_fetch_assoc($result)) {
	    echo "<tr>";
	    echo "<td>", $row['disc_label'], "</td>";
	    echo "<td>", $row['name'], "</td>";
	    echo "<td>", $row['dir'], "</td>";
        echo "</tr>";
		$rows[] = $row;
	}
    echo "</tbody></table>";
#	$columns=1;
#	$i=0;
#	for ($column=1; $column<=$columns; $column++) {
#		for (; $i<count($rows)*($column/$columns); $i++) {
#			echo $rows[$i];
#		}
#	}
#    return $rows;
}

connect();

$term = "";
if (array_key_exists('search', $_GET)) {
    $term = $_GET['search'];
}
$disc_id = "";
if (array_key_exists('disc_id', $_GET)) {
    $disc_id = $_GET['disc_id'];
}
$extant = 1;
if (array_key_exists('extant', $_GET)) {
    $extant = $_GET['extant'];
}

if ($term != "") {
    search($term);
}
else if ($disc_id == "") {
    # Available discs.
?>
<div class="col-md-6">
<h2>Optical Media Catalog - Discs</h2>
<table class="table">
<thead>
<tr>
<th>Printed Label</th>
<th>Volume Label</th>
<th>Disposition</th>
</tr>
</thead>
<tbody>
<?php
	$sql = "select id, label, name, status from disc";
	if ($extant == 1) {
	   $sql .= " where status=0";
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
			echo disc_row($rows[$i]);
		}
	}
?>
</tbody>
</table>
</div>
<?php
} else {
	# Disc contents.

	$sql  = "select file.* from disc";
	$sql .= " inner join file on disc.id=file.disc_id";
	$sql .= " where disc.id = '" . $disc_id . "'";
	$sql .= " order by file.dir";
	$result = mysql_query($sql);
	$prev_dir = "";
?>
    <div class="container"><div class="col-md-8">
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
</div>
</body>
</html>
