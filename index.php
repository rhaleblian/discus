<!doctype html>
<?php

require('model.php');

$baseurl = '';
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
      <a class="navbar-brand" href="<?php $basedir ?>">Disc Catalog</a>
    </div>

      <form class="navbar-form navbar-left" action="<?php $basedir ?>" method="get" role="search">
        <div class="form-group">
          <input type="text" class="form-control" name="search" placeholder="">
        </div>
        <button type="submit" class="btn btn-default">Search</button>
      </form>
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
    echo_disc_contents($disc_id);
}
?>
</div>
</div>
</body>
</html>
