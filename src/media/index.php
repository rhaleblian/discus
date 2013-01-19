<!doctype html public '-//W3C//DTD HTML 4.01//EN'
  'http://www.w3.org/TR/html4/strict.dtd'>
<html>
  <head>
    <link rel="stylesheet"
    href="http://www.w3.org/StyleSheets/Core/Chocolate" 
    type="text/css">
<?php

function echorow($row) {
	# print a row from the disc table.
	
	if ($row['status'] > 0) echo "<s>"; 
		echo "<a href=\"?disc=", $row['label'], "\">";
		echo $row['label'], "</a>";
		if ($row['status'] > 0) echo "</s>";
		echo "</br>\n";
}

$disc = $_GET['disc'];
$extant = $_GET['extant'];
if ($extant == "") {
	$extant = 1;
}

$ini_path = "../../../.config/media.ini";
$ini_array = parse_ini_file($ini_path);
$conn = mysql_connect($ini_array["host"],
                      $ini_array["user"],
                      $ini_array["passwd"]);
$result = mysql_select_db($ini_array["db"]);

if ($disc == "") {
    
    # Available discs.
?>
	<title>Optical Media Catalog</title>
  </head>
<body>
<?php
	$sql = "select label, status from disc";
	if ($extant == 1) {
	   $sql .= " where status = 0";
	}
	$sql .= " order by label";
	$result = mysql_query($sql);
	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		$rows[] = $row;
	}

	echo '<table border="1" cellpadding="4" cellspacing="4">';
	echo '<tr><td>',"\n";

	# Three columns.
	
	$i=0;
	for (; $i<count($rows)*.33; $i++) {
		echorow($rows[$i]);
	}

	echo "</td><td>\n";

	for (; $i<count($rows)*.66; $i++) {
		echorow($rows[$i]);
	}

	echo "</td><td>\n";

	for (; $i<count($rows); $i++) {
		echorow($rows[$i]);
	}

	echo "</td></tr></table>\n";

} else {
    
    # Disc contents.
?>
	<title>Optical Media Catalog for "<?php echo($disc) ?>"</title>
  </head>
  <body>
    <h3><?php echo($disc); ?></h3>
	<table cellpadding="4">
    <tr><td>Folder</td><td>File</td></tr>
<?php	
    $sql  = "select file.* from disc ";
    $sql .= "inner join file on disc.id=file.disc_id ";
    $sql .= "where disc.label like '" . $disc . "'";
	$result = mysql_query($sql);
	$lastdir = "";
	while ($row = mysql_fetch_assoc($result)) {
		echo "<tr>";
		if ($lastdir != $row['dir']) {
			echo "<td>",$row['dir'],"</td>";
		} else {
			echo "<td></td>";
		}
		echo "<td>",$row['name'],"</td>";
		echo "</tr>";
		$lastdir = $row['dir'];
	}
	echo "</table>";
}
?>
  </body>
</html>
