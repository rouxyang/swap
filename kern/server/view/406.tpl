<?php header('HTTP/1.1 406 Not Acceptable'); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>406 Not Acceptable</title>
</head>
<body>
<?php
if (kern\kernel::is_debug()) {
    echo '<h1>Visitor error: Not Acceptable</h1><hr>';
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
} else {
    echo '<h1>406 Not Acceptable</h1>';
}
?>
</body>
</html>
