# Class Astrology\Start

源码 https://github.com/wuding/astrology/blob/secret/src/Astrology/Start.php



## 公共属性

| #    | 属性            | 类型   | 默认值 | 描述           |
| ---- | --------------- | ------ | ------ | -------------- |
|      | $_disable_cache |        | 1      | 禁用缓存       |
|      | $cache          | object |        | 缓存驱动对象   |
|      | $cache_connect  |        |        | 缓存驱动连接   |
|      | $controller_key | string |        | 缓存控制器键名 |
|      | $route_key      | string |        | 缓存路由键名   |
|      |                 |        |        |                |
|      |                 |        |        |                |
|      |                 |        |        |                |
|      |                 |        |        |                |



## 公共方法

| #    | 方法               | 返回值 | 描述                               |
| ---- | ------------------ | ------ | ---------------------------------- |
|      | __construct()      |        | 初始化配置                         |
|      | initRoute()        |        | 初始化路由，获取模块和控制器名称   |
|      | loadController()   |        | 加载控制器，获取动作方法和请求参数 |
|      | composerAutoload() |        | 注册类加载                         |



## 全局变量

| #    | 变量            | 类型    | 描述     |
| ---- | --------------- | ------- | -------- |
|      | CONFIG          | array   | 配置     |
|      | MODULES         | array   | 模块     |
|      | $APP_MODULES    | array   | 模块     |
|      | MODULE_NAME     | string  | 模块名   |
|      | CONTROLLERS     | array   | 控制器   |
|      | _ROUTE          | array   | 路由信息 |
|      | CONTROLLER_NAME | string  | 控制器名 |
|      | SHIFT           | integer | 偏移量   |
|      | ACTION_NAME     | string  | 动作名   |
|      | METHOD_NAME     | string  | 方法名   |
|      | PARAMS          | array   | 请求参数 |
|      | PATH            | string  | 路由路径 |
|      |                 |         |          |
|      |                 |         |          |

