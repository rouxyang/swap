<?php header('HTTP/1.1 404 Not Found'); ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>404 Not Found</title>
</head>
<body>
<?php
if (swap\framework::is_debug()) {
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
