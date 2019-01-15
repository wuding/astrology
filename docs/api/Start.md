# Class Astrology\Start

源码 https://github.com/wuding/astrology/blob/secret/src/Astrology/Start.php



## 公共属性

| #    | 属性            | 类型   | 默认值 | 描述           |
| ---- | --------------- | ------ | ------ | -------------- |
| 1    | $_disable_cache |        | 1      | 禁用缓存       |
| 2    | $cache          | object |        | 缓存驱动对象   |
| 3    | $cache_connect  |        |        | 缓存驱动连接   |
| 4    | $controller_key | string |        | 缓存控制器键名 |
| 5    | $route_key      | string |        | 缓存路由键名   |



## 公共方法

| #    | 方法               | 返回值 | 描述                               |
| ---- | ------------------ | ------ | ---------------------------------- |
| 1    | __construct()      |        | 初始化配置                         |
| 2    | initRoute()        |        | 初始化路由，获取模块和控制器名称   |
| 3    | loadController()   |        | 加载控制器，获取动作方法和请求参数 |
| 4    | composerAutoload() |        | 注册类加载                         |



## 全局变量

| #    | 变量            | 类型    | 描述     |
| ---- | --------------- | ------- | -------- |
| 1    | CONFIG          | array   | 配置     |
| 2    | MODULES         | array   | 模块     |
| 3    | $APP_MODULES    | array   | 模块     |
| 4    | MODULE_NAME     | string  | 模块名   |
| 5    | CONTROLLERS     | array   | 控制器   |
| 6    | _ROUTE          | array   | 路由信息 |
| 7    | CONTROLLER_NAME | string  | 控制器名 |
| 8    | SHIFT           | integer | 偏移量   |
| 9    | ACTION_NAME     | string  | 动作名   |
| 10   | METHOD_NAME     | string  | 方法名   |
| 11   | PARAMS          | array   | 请求参数 |
| 12   | PATH            | string  | 路由路径 |

