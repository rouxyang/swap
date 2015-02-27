<?php header('HTTP/1.1 403 Forbidden'); ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>403 Forbidden</title>
</head>
<body>
<?php
if (swap\framework::is_debug()) {
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
