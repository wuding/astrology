# Class Astrology\Route

源码 



## 公共属性

| #    | 属性           | 类型  | 默认值 | 描述     | 缓存属性 |
| ---- | -------------- | ----- | ------ | -------- | -------- |
|      | $instance      |       |        | 实例     | 是       |
|      | $request_path  |       |        | 请求路径 | 可能     |
|      | $request_query |       |        | 请求查询 | 是       |
|      | $request_param | array |        | 请求参数 | 是       |
|      | $params        | array |        | 参数     | 是       |
|      | $query         |       |        | 查询     | 是       |
|      | $extension     |       |        | 扩展名   |          |
|      | $path          |       |        | 路径     | 肯定     |



## 公共方法

| #    | 方法                | 返回值 | 描述                       |
| ---- | ------------------- | ------ | -------------------------- |
|      | __construct()       |        | 导入路由规则，匹配请求路径 |
|      | getInstance()       | object | 获取路由类实例             |
|      | getRequestPath()    | string | 获取请求路径               |
|      | getRequestQuery()   |        | 获取请求查询               |
|      | getRequestParam()   |        | 获取请求参数               |
|      | getPath()           |        | 获取路径的一段             |
|      | getModuleName()     | string | 获取模块名称               |
|      | getControllerName() | string | 获取控制器名称             |
|      | getActionName()     | string | 获取动作名称               |
|      | getParams()         |        | 获取参数组                 |
|      | getParam()          |        | 获取参数值                 |
|      | getQueryArray()     | array  | 获取请求数组               |
|      | getScriptPath()     | string | 获取脚本路径               |
|      | fixName()           | string | 修正名称                   |
|      | getModules()        | array  | 扫描应用的所有模块         |
|      | appModules()        | array  | 获取模块数组               |
|      | getControllers()    | array  | 扫描模块下所有控制器       |
|      | appControllers()    | array  | 指定模块下的所有控制器     |
|      | array_merge_key()   | array  | 按键名合并数组             |

