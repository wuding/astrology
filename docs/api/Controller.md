# Class Astrology\Controller

源码



## 公共属性

| #    | 属性             | 类型    | 默认值 | 描述         |
| ---- | ---------------- | ------- | ------ | ------------ |
|      | $_methods        | array   |        | 类的所有方法 |
|      | $_method_name    | string  | false  | 动作方法名称 |
|      | $_view_script    | string  |        | 模板文件路径 |
|      | $_enable_session | boolean | 1      | 开启会话     |
|      | $_enable_view    | boolean | 1      | 开启视图     |
|      | $_session_id     | string  |        | 会话标识     |



## 公共方法

| #    | 方法             | 返回值 | 描述               | 属性缓存 |
| ---- | ---------------- | ------ | ------------------ | -------- |
|      | __construct()    |        | 初始化会话         |          |
|      | _getConfig()     | mixed  | 获取配置项值       |          |
|      | array_variable() | array  | 请求变量数组       |          |
|      | _var()           | mixed  | 变量值获取及过滤   |          |
|      | _get()           | mixed  | $_GET 值获取及过滤 |          |
|      | _NotFound()      |        | 未找到动作         |          |
|      | _getMethods()    | array  | 获取类的所有方法   |          |
|      | _getMethodName() | string | 获取动作方法       | 是       |
|      | _run()           |        | 执行动作           |          |
|      | __destruct()     |        | 调试和视图         |          |

