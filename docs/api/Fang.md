# Class Plugin\Robot\Fang

源码 https://github.com/wuding/astrology/blob/secret/src/Plugin/Robot/Fang.php



## 公共属性

| #    | 属性          | 类型    | 默认值 | 描述             |
| ---- | ------------- | ------- | ------ | ---------------- |
| 1    | $enable_relay | boolean |        | 开启转接任务     |
| 2    | $overwrite    | boolean |        | 重新下载         |
| 3    | $min_size     | integer |        | 重新下载限制大小 |
| 4    | $api_host     | string  |        | 接口主机         |
| 5    | $site_id      | integer | 1      | 站点标识         |
| 6    | $city_id      | integer | -1     | 城市标识         |
| 7    | $city_abbr    | string  |        | 城市缩写         |
| 8    | $city_path    | string  |        | 城市路径         |
| 9    | $city_name    | string  | -      | 城市名称         |
| 10   | $http_header  | array   |        | HTTP 请求头      |
| 11   | $paths        | array   |        | 本地保存地址     |
| 12   | $urls         | array   |        | 远程下载地址     |
| 13   | $relay_urls   | array   |        | 转接任务地址     |



## 公共方法

| #    | 方法             | 返回值 | 描述                     |
| ---- | ---------------- | ------ | ------------------------ |
| 1    | _init()          |        | 自定义初始化，重置属性值 |
| 2    | downloadZf()     |        | 下载列表首页             |
| 3    | updateStatus()   |        | 更新不需要改状态的       |
| 4    | optimizeStatus() |        | 更新状态                 |
| 5    | updateCache()    |        | 清除非正常状态的缓存队列 |
| 6    | downloadList()   |        | 下载出租列表             |
| 7    | parse_dom()      |        | 解析 DOM                 |
| 8    | check_detail()   |        | 检测出租详情数据         |
| 9    | xqCaption()      |        | 获取刷新时间和地点区域   |
| 10   | xqCrumbs()       |        | 获取地点区域             |
| 11   | xqBox()          |        | 获取租金价格和配套设施   |
| 12   | xqPrice()        |        | 获取租金价格和支付方式   |
| 13   | xqTable()        |        | 获取详情                 |
| 14   | xqFacility()     | string | 获取配套设施             |
| 15   | xqDescription()  |        | 获取房源描述             |
| 16   | xqFymsList()     |        | 获取房源描述列表         |
| 17   | check_list()     |        | 检测出租列表数据         |
| 18   | downloadPc()     |        | 下载 PC 版列表           |
| 19   | downloadDetail() |        | 下载出租详情             |
| 20   | downloadCity()   |        | 下载城市列表             |
| 21   | parseCity()      |        | 解析城市列表             |



## 方法详情

### _init()

重置属性值：

paths, relay_urls, city_id,

可以省略：

http_header, api_host, urls, city_path, city_name



### downloadZf()

1. 下载列表首页
2. 解析 DOM，获取页数，检测列表
3. 返回结果，转接任务



### downloadList()

1. 下载出租列表
2. 解析 DOM，检测列表
3. 返回结果，转接任务



### downloadDetail()

1. 按页获取数据库条目和总页数
2. 下载出租详情，解析 DOM，检测详情
3. 返回结果



### parse_dom($str, $charset = null, $id = null, $from_encoding = null, $replace = [])

| 参数 | 类型 | 描述 |
| -------- | :--: | ---- |
| $str | string | html |
| $charset | string | html 编码 |
| $id | string | 元素 id |
| $from_encoding | string | 源编码 |
| $replace | array | html 替换 |

1. 转码与替换
2. 获取文档对象或者元素



### check_list($doc)

1. 检测有 data-bg 属性的列表
2. 获取标题 h3 和图片 img
3. 分析 span
   - 价格 new
   - 时间 flor
   - 标签 red-z
   - 实名认证 zfsmrz
   - 顶 tag-yell
   - 精 tag-jing
4. 分析 p
   - 户型、租赁方式
   - 区县、小区
5. 数据库检测条目



### check_detail($doc, $row)

1. 异常处理：404、获取信息出错、请求超时；异常消息队列
2. 幻灯图片
3. 分析 section
   - 时间、区域 xqCaption mb8
   - 价格、设施 xqBox mb8
   - 描述、小区 mBox
4. 返回数据





### xqCaption($node, $data = [])

getSectionArea

1. 分析 p
   - 面包屑：地区
   - 刷新时间
2. 返回数据



### xqBox($section, $data = [])

getSectionPrice

1. 分析 div

   - 标签 stag
   - 价格、支付 price-box mt20
   - 详情 bb pdY10
   - 配套设施 ptss-zf pdY14

2. 返回数据


### xqDescription($node, $data = [])

getSectionDescription

1. 分析 div
   - 房源描述 fymsList pdX20
2. 返回数据



### xqCrumbs($node_list, $data = [])

getAreaCrumbs

1. 分析 a：区县、乡镇、小区
2. 数据库检测条目：区县、乡镇、小区
3. 返回数据



### xqPrice($node, $data = [])

getRentPay

1. 分析 span 根据顺序判断
2. 返回数据



### xqTable($node, $data = [])

getRentDetail

1. 分析 li
2. 检测：租赁方式、户型、建筑面积、楼层、朝向、装修、入住时间
3. 总楼层
4. 返回数据



### xqFacility($node)

getRentFacility

1. 分析 span 检测 class
2. 返回数据



### xqFymsList($node, $data = [])

getDescriptionList

1. 分析 li
2. 检测：房源亮点
3. 返回数据

