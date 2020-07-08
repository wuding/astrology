API

https://github.com/Binaryify/NeteaseCloudMusicApi

https://github.com/metowolf/Meting

https://github.com/QiuYaohong/kuwoMusicApi

获取歌词

https://music.163.com/weapi/song/lyric?csrf_token=

获取音乐-url

https://github.com/Toneyh/kuwo-music-Api

https://github.com/kasuganosoras/SakuraAPI-Kuwo

https://music.163.com/song/media/outer/url?id={{id}}.mp3

https://music.163.com/weapi/song/enhance/player/url/v1?csrf_token=



## 歌手列表

按类型、首字母下载，解析歌手主页ID



## 歌手页面

解析姓名、热门歌曲，更新歌手信息



## *歌手专辑

https://music.163.com/artist/album?id=28387245&limit=120&offset=0



## *歌手 MV

https://music.163.com/artist/mv?id=2116&limit=200&offset=0



## *艺人介绍

https://music.163.com/artist/desc?id=3076



## 歌词

根据歌曲 ID 通过 API 获取歌词信息并生成日志（如果已经有日志则跳过），下载 3 种歌词并记录到数据库



## 音频

根据歌曲 ID 通过 API 获取音频信息并生成日志（如记录已下载并不再次下载则跳过），记录音频和地址信息到数据库；

下载音频文件，更新数据库音频和地址表的状态、歌曲表的下载状态



## *用户页

https://music.163.com/user/home?id=29879272

动态、关注、粉丝

### 听歌排行

https://music.163.com/user/songs/rank?id=29879272

### 创建的专栏

https://music.163.com/series?id=1195050

#### 话题

https://music.163.com/topic?id=14302051

有多首歌曲列表

### 创建的电台

https://music.163.com/djradio?id=203

#### 电台节目

https://music.163.com/program?id=2067761424

创建和收藏的歌单



## *歌单

https://music.163.com/playlist?id=20591637

