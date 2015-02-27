<?php header('HTTP/1.1 400 Bad Request'); ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>400 Bad Request</title>
</head>
<body>
<?php
if (swap\framework::is_debug()) {
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
