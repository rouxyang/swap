<?php header('HTTP/1.1 405 Method Not Allowed'); header('Allow: ' . $e->get_value('allow_list')); ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>405 Method Not Allowed</title>
</head>
<body>
<?php
if (swap\framework::is_debug()) {
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
