<?php header('HTTP/1.1 403 Forbidden'); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>403 Forbidden</title>
</head>
<body>
<?php
if (kern\kernel::is_debug()) {
    echo '<h1>Visitor error: Forbidden</h1><hr>';
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
} else {
    echo '<h1>403 forbidden</h1>';
}
?>
</body>
</html>
