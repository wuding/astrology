<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<title><?=$title?></title>
</head>

<body style="margin: 0">
<?=$html?>
<fieldset style="/*padding: 0;*/">
<legend>以下是内容预览</legend>
<?=$pic?>
<div id="description"></div>
<div id="content" style="word-wrap: break-word; display: block"><?=$name?></div>
</fieldset>
<script>
content.style.display = 'none';
content.style.width = description.offsetWidth + 'px';
content.style.display = 'block';
</script>
<?php
include __DIR__ . '/../_helper/tongji.php';
?>

</body>
</html>
