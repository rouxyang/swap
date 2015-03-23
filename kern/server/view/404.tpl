<?php header('HTTP/1.1 404 Not Found'); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>404 Not Found</title>
</head>
<body>
<?php
if (kern\framework::is_debug()) {
    echo '<h1>Visitor error: Not Found</h1><hr>';
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
} else {
    echo '<h1>404 not found</h1>';
}
?>
</body>
</html>
