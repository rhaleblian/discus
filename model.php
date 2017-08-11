<?php

function connect() {
    $ini_path = '/etc/yoyodyne/media.ini';
    $ini = parse_ini_file($ini_path);
    $pdo = new PDO('pgsql:host=' . $ini['host'] 
    . ';dbname=' . $ini['database']
    . ';user=' . $ini['user'] 
    . ';password=' . $ini['password']); 
    return $pdo; 
} 

function status_string($code) { 
    if ($code == 0) { return "extant"; } 
    else { return "non-extant"; } 
} 

function disc_label($pdo, $id) { 
    $sql = "SELECT label from disc WHERE id=" . $id; 
    foreach ($pdo->query($sql) as $row) { 
        $label = $row['label']; 
    } 
    return $label; 
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

function echo_discs($pdo, $extant) { 
	$sql = "select id, label, name, status from disc"; 
	if ($extant == 1) { 
	  $sql .= " where status=0"; 
	} 
	$sql .= " order by label"; 

    foreach ($pdo->query($sql) as $row) { 
        echo html_disc_row($row); 
    } 
} 

function echo_disc_contents($pdo, $disc_id) { 
	# Echo disc contents. 

    $sql  = "select file.* from disc"; 
	$sql .= " inner join file on disc.id=file.disc_id"; 
	$sql .= " where disc.id = '" . $disc_id . "'"; 
	$sql .= " order by file.dir"; 

    echo '<h2>', disc_label($pdo, $disc_id), '</h2>', "\n"; 
    echo '<div class="col-md-8">', "\n"; 
    echo '<table class="table">'; 
    echo '<thead><tr><td>file</td><td>folder</td></tr></thead>'; 
    echo '<tbody>'; 
    foreach ($pdo->query($sql) as $row) { 
	    echo "<tr>"; 
	    echo "<td>", $row['name'], "</td>"; 
	    echo "<td>", $row['dir'], "</td>"; 
        echo "</tr>"; 
	} 
    echo "</tbody></table>"; 
    echo "</div>\n"; 
} 

function echo_disc_contents_flat($pdo, $disc_id) { 
	# Echo disc contents traditionally -
    # as relative paths from the disc root. 

    $sql  = "select file.* from disc"; 
	$sql .= " inner join file on disc.id=file.disc_id"; 
	$sql .= " where disc.id = '" . $disc_id . "'"; 
	$sql .= " order by file.dir"; 

    echo '<h2>', disc_label($pdo, $disc_id), '</h2>', "\n"; 
    echo '<div class="col-md-8">', "\n"; 
    foreach ($pdo->query($sql) as $row) { 
	    if ($row['name'] != '') { 
            $dir = preg_replace('#^\.?/?#', '', $row['dir']); 
            if ($dir != '') $dir = $dir . '/'; 
            echo "<p>", $dir, $row['name'], "</p>"; 
        } 
    } 
    echo "</div>\n"; 
} 

function search($pdo, $term) { 
    # Return rows where term was found in names. 

    $wildcard = "%" . $term . "%"; 
    $sql  = "SELECT * FROM disc INNER JOIN file ON disc.id=file.disc_id"; 
    $sql .= " WHERE file.dir LIKE '" . $wildcard . "'"; 
    $sql .= " OR disc.label LIKE '" . $wildcard . "'"; 
    $sql .= " OR file.name LIKE '" . $wildcard . "';"; 
    
    return $pdo->query($sql);
}

?>
