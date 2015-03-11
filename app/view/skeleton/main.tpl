<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php
static::echo_title();
static::echo_metas();
static::echo_favicon();
static::echo_top_links();
?>
</head>
<?php
echo $_html;
static::echo_bottom_links();
?>
</html>
