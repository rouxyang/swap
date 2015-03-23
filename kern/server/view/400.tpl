<?php header('HTTP/1.1 400 Bad Request'); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>400 Bad Request</title>
</head>
<body>
<?php
if (kern\framework::is_debug()) {
    echo '<h1>Visitor error: Bad Request</h1><hr>';
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
} else {
    echo '<h1>400 bad request</h1>';
}
?>
</body>
</html>
