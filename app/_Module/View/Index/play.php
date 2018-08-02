<!doctype html>
<html style="width: 100%; height: 100%;">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
    <title><?=$title?></title>
	<script type="text/javascript" src="http://192.168.100.4/chplayer-master/chplayer/chplayer.min.js"></script>
</head>

<body style="width: 100%; height: 100%; margin: 0;">
<form action="/s?debug" method="get" onsubmit="return play();" style="width: 100%; max-width: 800px; margin: 0 auto;<?php if ($hide) { echo 'display: none;'; } ?>">
	<div style="width: 90%; float: left">
		<input name="q" value="<?=htmlspecialchars($url)?>" style="width: 100%" id="url" placeholder="请输入m3u8地址">
		<input type="hidden" name="debug" value="">
	</div>	
	<div style="width: 10%; float: left">
		<button type="submit" style="width: 100%; max-height: 23px;">&nbsp; &crarr; &nbsp;</button>
	</div>
</form>
<div id="video" style="width: 100%; height: 100%; max-width: 800px; max-height: 450px; margin: 0 auto;"></div>
<script>
var url = '';
var obj = {
	container: '#video',
	variable: 'player',
	video:[
		[ url, '', '', 0 ]
	]
};
var player;

function play() {
	url = document.getElementById( 'url' ).value;
	obj.video[0] = [ url, '', '', 0 ];
	console.log( JSON.stringify( obj ) );
	player = new chplayer( obj ); // 
	return false;
}

if (document.getElementById( 'url' ).value) {
	play();
}
</script>

<?php
include __DIR__ . '/../_helper/tongji.php';
?>
</body>
</html>


