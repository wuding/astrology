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

