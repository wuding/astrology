# Class Astrology\Controller

源码 https://github.com/wuding/astrology/blob/secret/src/Astrology/Controller.php



## 公共属性

| #    | 属性             | 类型    | 默认值 | 描述         |
| ---- | ---------------- | ------- | ------ | ------------ |
| 1    | $_methods        | array   |        | 类的所有方法 |
| 2    | $_method_name    | string  | false  | 动作方法名称 |
| 3    | $_view_script    | string  |        | 模板文件路径 |
| 4    | $_enable_session | boolean | 1      | 开启会话     |
| 5    | $_enable_view    | boolean | 1      | 开启视图     |
| 6    | $_session_id     | string  |        | 会话标识     |



## 公共方法

| #    | 方法             | 返回值 | 描述               | 属性缓存 |
| ---- | ---------------- | ------ | ------------------ | -------- |
| 1    | __construct()    |        | 初始化会话         |          |
| 2    | _getConfig()     | mixed  | 获取配置项值       |          |
| 3    | array_variable() | array  | 请求变量数组       |          |
| 4    | _var()           | mixed  | 变量值获取及过滤   |          |
| 5    | _get()           | mixed  | $_GET 值获取及过滤 |          |
| 6    | _NotFound()      |        | 未找到动作         |          |
| 7    | _getMethods()    | array  | 获取类的所有方法   |          |
| 8    | _getMethodName() | string | 获取动作方法       | 是       |
| 9    | _run()           |        | 执行动作           |          |
| 10   | __destruct()     |        | 调试和视图         |          |

