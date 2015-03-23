<?php header('HTTP/1.1 405 Method Not Allowed'); header('Allow: ' . $e->get_value('allow_list')); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>405 Method Not Allowed</title>
</head>
<body>
<?php
if (kern\framework::is_debug()) {
    echo '<h1>Visitor error: Method Not Allowed</h1><hr>';
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
} else {
    echo '<h1>405 method not allowed</h1>';
}
?>
</body>
</html>
