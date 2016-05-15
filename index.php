<!doctype html>
<?php

require 'model.php';

$baseurl = 'ray/discus';
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
        <title>Discus</title>

        <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
        <link rel="icon" href="/favicon.png" type="image/x-icon">
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
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
                        <a class="navbar-brand" href="<?php $baseurl ?>">Discus</a>
                    </div>
                    <form class="navbar-form navbar-left" action="<?php $basedir ?>" method="get" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control" name="search" placeholder="">
                        </div>
                        <button type="submit" class="btn btn-default">Search</button>
                    </form>
                </div><!-- /.navbar-collapse -->
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
                            <th>Volume Name</th>
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
        </div><!-- /.container-fluid -->
    
        <script src="https://code.jquery.com/jquery-2.2.3.min.js" integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    
    </body>
</html>
