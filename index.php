<!doctype html>
<?php

function connect() {
    $ini_path = "/home/halebs/.config/yoyodyne/media.ini";
    $ini_array = parse_ini_file($ini_path);
    $conn = mysql_connect($ini_array["host"],
                          $ini_array["username"],
                          $ini_array["password"]);
    $result = mysql_select_db($ini_array["db"]);
    return $conn;
}

function status_string($code) {
    if ($code == 0) { return "extant"; }
    else { return "non-extant"; }
}

function disc_label($id) {
    $sql = "SELECT label from disc WHERE id=" . $id;
    $result = mysql_query($sql);
    return 
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
	$prev_dir = "";

    echo '<h2>', disc_label($disc_id), '</h2>', "\n";
    echo '<div class="col-md-8">', "\n";
	while ($row = mysql_fetch_assoc($result)) {
		if ($prev_dir != $row['dir']) {
			echo "<h4>", $row['dir'], "</h4>";
    		$prev_dir = $row['dir'];
		}
		echo "<p>", $row['name'], "</p>";
	}
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
    echo "<p>" . $sql . "</p>\n";
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
?>


<html lang="en">
<head>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
<!--<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">-->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
<link rel="icon" href="/favicon.png" type="image/x-icon">
<title>Disc Catalog</title>
</head>
<body class="body">
<div class="container">

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/media">Disc Catalog</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
<!--
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
            <li class="divider"></li>
            <li><a href="#">One more separated link</a></li>
          </ul>
        </li>
      </ul>
-->
      <form class="navbar-form navbar-left" action="/media" method="get" role="search">
        <div class="form-group">
          <input type="text" class="form-control" name="search" placeholder="Search">
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>
      <!--
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#">Link</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Action</a></li>
            <li><a href="#">Another action</a></li>
            <li><a href="#">Something else here</a></li>
            <li class="divider"></li>
            <li><a href="#">Separated link</a></li>
          </ul>
        </li>
      </ul>
      -->
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div class="col-md-12">
<?php
if ($term != "") {
    search($term);
}
else if ($disc_id == "") {
    # Available discs.
?>
    <div class="col-md-6">
        <h2>Discs</h2>
            <table class="table">
            <thead>
                <tr>
                <th>Printed Label</th>
                <th>Volume Label</th>
                <th>Disposition</th>
                </tr>
            </thead>
            <tbody>
<?php echo_discs($extant); ?>
            </tbody>
        </table>
    </div>
<?php
} else {
    echo_disc_contents_flat($disc_id);
}
?>
</div>
</div>
</body>
</html>
