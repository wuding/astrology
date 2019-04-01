<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
    <title><?=$title?></title>
	<!--script type="text/javascript" src="http://192.168.100.4/chplayer-master/chplayer/chplayer.min.js"></script-->
	<script type="text/javascript" src="http://cpn.red/chplayer/chplayer.min.js"></script>
	<style>
html {
	width: 100%;
	height: 100%;
}

body {
	width: 100%;
	height: 100%;
	margin: 0;
}

.container {
	height: 100%;
	width: 100%;
	max-width: 800px;
	margin: 0 auto;
}

form {
	overflow:hidden;
	<?=$hide ? 'display: none;' : ''?>
}

.left {
	width: 90%;
	float: left;
}

.right {
	width: 10%;
	float: left;
}

#url,
button
{
	width: 100%;
	display: block;
}

#url {
	
}

button {
	max-height: 23px;
}

#video {
	height: 100%;
	max-height: 450px;
	display: none;
}

ul {
	overflow: hidden;
}

li {
	width: 100%;
	float: left;
}

fieldset {
    margin: 0 auto;
    padding: 0 0 10px 0;
    border-width: 1px;
    border-right: none;
    border-bottom: none;
    border-left: none;
    overflow: hidden;
	/*width: 320px;*/
}

legend {
    margin: 0 auto;
	padding: 0 10px;
}

fieldset div {
	overflow: hidden;
	margin: 0 auto;
	padding: 10px;
	width: 300px;
	text-align: center;
}

blockquote {
	margin: 0;
	width: 50%;
	float: left;
	line-height: 100px;
}

p {
	margin: 0;
	width: 120px;
	width: 50%;
	float: left;
}

img {
	width: 120px;
	height: 120px;
	background-image: url(/img/wx_hbfl.jpg);
	background-size: 140px;
    background-position: -10px -50px;
}

p i {
	display: block;
	clear: left;
	line-height: 14px;
	font-size: 14px;
	font-style: normal;
}

@media (min-width: 640px) {
	li {
		width: 50%;
	}
}

@media (min-width: 960px) {
	li {
		width: 33%;
	}
}
	</style>
</head>

<body>
<div class="container">
	<form action="/play" method="get" onsubmit="return play();">
		<div class="left">
			<input id="url" name="q" value="<?=htmlspecialchars($like ? : $url)?>" placeholder="请输入m3u8地址或搜索影片名称" onfocus="this.select()" data-url="<?=htmlspecialchars($url)?>">
			<input type="hidden" name="debug" value="">
		</div>	
		<div class="right">
			<button type="submit">&nbsp; &crarr; &nbsp;</button>
		</div>
	</form>
	<div id="video"></div>
	<div id="audio" style="display: none">
		<audio src="http://developer.mozilla.org/@api/deki/files/2926/=AudioTest_(1).ogg" autoplay controls="controls">
  			Your browser does not support the <code>audio</code> element.
		</audio>
	</div>
	<ul>
	<?php
	foreach ($arr as $key => $value) {
		echo "<li><a href='/play/$value->name'>$value->title</a></li>";
	}
	?>
	</ul>
	<fieldset>
		<legend>更多</legend>
		<div>
			<blockquote>
				<a href="https://www.cpn.red/" target="_blank">www.cpn.red</a>
			</blockquote>
			<p>
				<a href="/img/wx_hbfl.jpg" target="_qr">
					<img src="/img/px.png">
					<i>微信扫码加群</i>
				</a>
			</p>
		</div>
	</fieldset>
</div>

<script>
var obj = {
	container: '#video',
	variable: 'player',
	video:[
		[ url, '', '', 0 ]
	]
};
var player;

function play(u, tt) {
	video.style.display = 'block'
	u = u ? u : url.value
	if (u.match(/^http/i)) {
		if (!tt) {
			document.title = '在线M3U8播放器'
		}
		
		obj.video[0] = [ u, '', '', 0 ]
		console.log( JSON.stringify( obj ) )
		if (u.match(/\.aac$/i)) {
			document.getElementsByTagName('audio')[0].src = u
			video.style.display = 'none'
			audio.style.display = 'block'
			return false
		}
		player = new chplayer( obj )
		return false
	} else {
		return true
	}
}

m3u8_url = url.getAttribute('data-url')
if (m3u8_url) {
	play(m3u8_url, '<?=htmlspecialchars($like)?>');
} else {
	url.focus();
}
</script>

<?php
include __DIR__ . '/../_helper/tongji.php';
?>
</body>
</html>


