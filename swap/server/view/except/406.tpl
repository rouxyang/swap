<?php header('HTTP/1.1 406 Not Acceptable'); ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>406 Not Acceptable</title>
</head>
<body>
<?php
if (swap\framework::is_debug()) {
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
