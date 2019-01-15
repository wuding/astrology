# Class Astrology\Route

源码 https://github.com/wuding/astrology/blob/secret/src/Astrology/Route.php



## 公共属性

| #    | 属性           | 类型  | 默认值 | 描述     | 缓存属性 |
| ---- | -------------- | ----- | ------ | -------- | -------- |
| 1    | $instance      |       |        | 实例     | 是       |
| 2    | $request_path  |       |        | 请求路径 | 可能     |
| 3    | $request_query |       |        | 请求查询 | 是       |
| 4    | $request_param | array |        | 请求参数 | 是       |
| 5    | $params        | array |        | 参数     | 是       |
| 6    | $query         |       |        | 查询     | 是       |
| 7    | $extension     |       |        | 扩展名   |          |
| 8    | $path          |       |        | 路径     | 肯定     |



## 公共方法

| #    | 方法                | 返回值 | 描述                       |
| ---- | ------------------- | ------ | -------------------------- |
| 1    | __construct()       |        | 导入路由规则，匹配请求路径 |
| 2    | getInstance()       | object | 获取路由类实例             |
| 3    | getRequestPath()    | string | 获取请求路径               |
| 4    | getRequestQuery()   |        | 获取请求查询               |
| 5    | getRequestParam()   |        | 获取请求参数               |
| 6    | getPath()           |        | 获取路径的一段             |
| 7    | getModuleName()     | string | 获取模块名称               |
| 8    | getControllerName() | string | 获取控制器名称             |
| 9    | getActionName()     | string | 获取动作名称               |
| 10   | getParams()         |        | 获取参数组                 |
| 11   | getParam()          |        | 获取参数值                 |
| 12   | getQueryArray()     | array  | 获取请求数组               |
| 13   | getScriptPath()     | string | 获取脚本路径               |
| 14   | fixName()           | string | 修正名称                   |
| 15   | getModules()        | array  | 扫描应用的所有模块         |
| 16   | appModules()        | array  | 获取模块数组               |
| 17   | getControllers()    | array  | 扫描模块下所有控制器       |
| 18   | appControllers()    | array  | 指定模块下的所有控制器     |
| 19   | array_merge_key()   | array  | 按键名合并数组             |

