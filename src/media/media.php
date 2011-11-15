<head><title>Data/Media Library</title>
<link rel="stylesheet" type="text/css"
 media="screen" href="style.php">
</head>
<body>
<?php

$disc = $_GET['disc'];

$conn = mysql_connect("localhost", "rhaleblian");
$result = mysql_select_db("media");

if ($disc == "") {
    
    # Available discs.
    
	$sql = "select label, status from disc order by label";
	$result = mysql_query($sql);
	$rows = array();
	while ($row = mysql_fetch_assoc($result)) {
		$rows[] = $row;
	}

	echo '<table cellpadding="16">';
	echo "<tr><td>\n";

	$i=0;
	for (; $i<count($rows)/3; $i++) {
		$row = $rows[$i];
        if ($row['status'] > 0) echo "<s>"; 
		echo "<a href=\"?disc=", $row['label'], "\">";
		echo $row['label'], "<br/>\n";
		echo "</a>\n";
        if ($row['status'] > 0) echo "</s>"; 
	}

	echo "</td><td>\n";

	for (; $i<count($rows)*.66; $i++) {
		$row = $rows[$i];	
		echo "<a href=\"?disc=", $row['label'], "\">";
		echo $row['label'],"<br/>\n";
		echo "</a>\n";
	}

	echo "</td><td>\n";

	for (; $i<count($rows); $i++) {
		$row = $rows[$i];	
		echo "<a href=\"?disc=", $row['label'], "\">";
		echo $row['label'],"<br/>\n";
		echo "</a>\n";
	}

	echo "</td></tr></table>\n";

} else {
    
    # Disc contents.
	
    $sql  = "select file.* from disc ";
    $sql .= "inner join file on disc.id=file.disc_id ";
    $sql .= "where disc.label like '" . $disc . "'";
	$result = mysql_query($sql);
    echo '<h3>',$disc,'</h3>';
	echo '<table cellpadding="4">';
    echo '<tr><td>Folder</td><td>File</td></tr>';
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
