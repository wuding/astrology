<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title><?=$title?></title>
    <script type="text/javascript" src="https://<?=$cdn_host?>/github/niandeng-ckplayer/chplayer/chplayer/chplayer.min.js"></script>
    <!--script type="text/javascript" src="http://cpn.red/chplayer/chplayer.min.js"></script-->
    <link rel="stylesheet" type="text/css" href="http://<?=$cdn_host?>/site/yingmi/v1/css/plain.css">
</head>

<body>
<div class="container">
    <form action="/play" method="get" onsubmit="return play();">
        <div class="left">
            <input id="url" name="q" value="<?=htmlspecialchars($like ? : $url)?>" placeholder="请输入m3u8地址或搜索影片名称" onfocus="this.select()" data-url="<?=htmlspecialchars($url)?>" data-title="<?=htmlspecialchars($like)?>">
            <input type="hidden" name="debug" value="">
        </div>
        <div class="right">
            <button type="submit">&nbsp; &crarr; &nbsp;</button>
        </div>
    </form>
    <div id="video"></div>
    <div id="audio" style="display: none">
        <!-- http://developer.mozilla.org/@api/deki/files/2926/=AudioTest_(1).ogg -->
        <audio src="" autoplay="autoplay" controls="controls">
            Your browser does not support the <code>audio</code> element.
        </audio>
    </div>
    <ul>
    <?php
    $i = 0;
    foreach ($arr as $key => $value) {
        $i++;
        echo "<li><b>$i. </b><a href='/play/$value->name'>$value->title</a></li>";
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
                <a href="//<?=$cdn_host?>/site/yingmi/v1/img/wx_hbfl.jpg" target="_qr">
                    <img src="//<?=$cdn_host?>/img/px.png">
                    <i>微信扫码加群</i>
                </a>
            </p>
        </div>
    </fieldset>
</div>

<script type="text/javascript" src="//<?=$cdn_host?>/site/yingmi/v1/js/play.js"></script>
<?php
include __DIR__ . '/../_helper/tongji.php';
?>
</body>
</html>


