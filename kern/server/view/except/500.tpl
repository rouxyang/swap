<?php header('HTTP/1.1 500 Internal Server Error'); ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>500 Internal Server Error</title>
</head>
<body>
<?php
if (kern\framework::is_debug()) {
    if ($e instanceof kern\error) {
        echo '<h1>Internal Server Error: Hard Error</h1><hr>';
    } else if ($e instanceof kern\except) {
        echo '<h1>Internal Server Error: Soft Except</h1><hr>';
    } else {
        echo '<h1>Internal Server Error: Unknown Exception</h1><hr>';
    }
    echo '<h2>Detailed Information:</h2>';
    echo '<pre>' . $e->getMessage() . '</pre>';
    echo 'in file: ' . $e->getFile() . '<br>';
    echo 'in line: ' . $e->getLine();
    echo '<h3>Code Trace</h3>';
    echo '<pre>' . var_export($e->getTrace(), true) . '</pre>';
} else {
    echo '<h1>Internal Server Error</h1><hr>';
    echo '<h2>Please contact web master.</h2>';
}
?>
</body>
</html>
