<head><title>Data/Media Library</title>
<link rel="stylesheet" type="text/css"
 media="screen" href="style.php">
</head>
<body>
<?php

$disc = $_GET['disc'];

$conn = mysql_connect("localhost", "root", "23masons");
$result = mysql_select_db("media");

if ($disc == "") {
	//	$sql = "select disc from file group by disc";
	$sql = "select label from disc";
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
		echo "<a href=\"?disc=", $row['label'], "\">";
		echo $row['label'],"<br/>\n";
		echo "</a>\n";
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
	//	$sql = "select * from file where disc like '" . $disc . "'";
	$sql = "select file.* from disc inner join file on disc.id=file.disc_id where disc.label like '" . $disc . "'";
	$result = mysql_query($sql);
	echo '<table cellpadding="4"><tr><td>Disc</td><td>Folder</td><td>File</td></tr>';
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
