<!doctype html>
<html>
<head>
<meta charset="utf-8">
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
