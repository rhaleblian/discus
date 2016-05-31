<?php

function connect() {
  $ini_path = getenv('/etc/yoyodyne/media.ini';
  $ini_array = parse_ini_file($ini_path);
  $conn = mysql_connect($ini_array["host"],
                        $ini_array["user"],
                        $ini_array["password"]);
  $result = mysql_select_db($ini_array["database"]);
  return $conn;
}

function status_string($code) {
    if ($code == 0) { return "extant"; }
    else { return "non-extant"; }
}

function disc_label($id) {
    $sql = "SELECT label from disc WHERE id=" . $id;
    $result = mysql_query($sql);
    $row = mysql_fetch_assoc($result);
    return $row['label'];
}

function html_disc_row($row) {
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

function echo_discs($extant) {
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
			echo html_disc_row($rows[$i]);
		}
	}
}

function echo_disc_contents($disc_id) {
	# Echo disc contents.

	$sql  = "select file.* from disc";
	$sql .= " inner join file on disc.id=file.disc_id";
	$sql .= " where disc.id = '" . $disc_id . "'";
	$sql .= " order by file.dir";
	$result = mysql_query($sql);

    echo '<h2>', disc_label($disc_id), '</h2>', "\n";
    echo '<div class="col-md-8">', "\n";
    echo '<table class="table">';
    echo '<thead><tr><td>file</td><td>folder</td></tr></thead>';
    echo '<tbody>';
	while ($row = mysql_fetch_assoc($result)) {
	    echo "<tr>";
	    echo "<td>", $row['name'], "</td>";
	    echo "<td>", $row['dir'], "</td>";
        echo "</tr>";
	}
    echo "</tbody></table>";
    echo "</div>\n";
}

function echo_disc_contents_flat($disc_id) {
	# Echo disc contents traditionally - as relative paths from the disc root.

	$sql  = "select file.* from disc";
	$sql .= " inner join file on disc.id=file.disc_id";
	$sql .= " where disc.id = '" . $disc_id . "'";
	$sql .= " order by file.dir";
	$result = mysql_query($sql);

    echo '<h2>', disc_label($disc_id), '</h2>', "\n";
    echo '<div class="col-md-8">', "\n";
	while ($row = mysql_fetch_assoc($result)) {
	    if ($row['name'] != '') {
            $dir = preg_replace('#^\.?/?#', '', $row['dir']);
            if ($dir != '') $dir = $dir . '/';
            echo "<p>", $dir, $row['name'], "</p>";
        }
    }
    echo "</div>\n";
}

function search($term) {
    echo "<h2>Search results for '", $term, "'</h2>";
    echo '<table class="table">';
    echo '<thead><tr><td>disc</td><td>file</td><td>folder</td></tr></thead>';
    echo '<tbody>';
    $sql = "SELECT * FROM entry WHERE name LIKE '%" . $term . "%' OR dir LIKE '%" . $term . "%' LIMIT 256;";
    #echo "<p>" . $sql . "</p>\n";
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
}

?>
