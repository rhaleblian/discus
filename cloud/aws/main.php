<?php
function echo_row($row) {
	# print a row from the disc table.
	$id = $row['id'];
	$name = $row['name'];
	$label = $row['label'];
	$status = $row['status'];
	if ($status > 0) echo "<s>";
	echo "<a href=\"?disc_id=", $id, "\">";
	if (strlen($name) && strlen($label) && ($name != $label))
		echo $label, " [", $name, "]</a>";
	else if (strlen($label))
		echo $label, "</a>";
	else
		echo $name, "</a>";
	if ($status > 0) echo "</s>";
}
?>

<!DOCTYPE html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Media</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <link rel="stylesheet" href="css/404.css">
        <script src="http://ray.haleblian.com/js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
<body>

<?php
$disc_id = $_GET['disc_id'];
$extant = $_GET['extant'];
if ($extant == "") {
	$extant = 1;
}

$ini_path = "media.ini";
$ini_array = parse_ini_file($ini_path);
$conn = mysql_connect($ini_array["host"],
                      $ini_array["user"],
                      $ini_array["passwd"]);
$result = mysql_select_db($ini_array["db"]);

if ($disc_id == "") {
    # Available discs.

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
    echo '<div class="container">';
    echo '<h1>Discs</h1>';
	for ($column=1; $column<=$columns; $column++) {
		for (; $i<count($rows)*($column/$columns); $i++) {
			echo_row($rows[$i]);
    		echo "</br>\n";
		}
	}
    echo "</div>";

} else {
	# Disc contents.

	$sql  = "select name from disc";
	$sql .= " where disc.id = '" . $disc_id . "'";
	$result = mysql_query($sql);
    $row = mysql_fetch_assoc($result);
    echo '<h1>', $row['name'], '</h1>';

	$sql  = "select disc.name,file.* from disc";
	$sql .= " inner join file on disc.id=file.disc_id";
	$sql .= " where disc.id = '" . $disc_id . "'";
	$result = mysql_query($sql);
	$previous_dir = "";
	while ($row = mysql_fetch_assoc($result)) {
		if ($previous_dir != $row['dir']) {
			echo "<h4>", $row['dir'], "</h4>";
    		$previous_dir = $row['dir'];
		}
		echo $row['name'], '</br>';
	}
}
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="http://ray.haleblian.com/js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
<script src="http://ray.haleblian.com/js/plugins.js"></script>
<script src="http://ray.haleblian.com/js/main.js"></script>

</body>
</html>
