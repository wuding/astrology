# Class Astrology\Database



源码 https://github.com/wuding/astrology/blob/master/src/Astrology/Database.php



## 公共属性

| #    | 属性            | 类型    | 默认值    | 描述           |
| ---- | --------------- | ------- | --------- | -------------- |
|      | $adapter        | object  |           | 适配器         |
|      | $driver         | string  | pdo_mysql | 数据库驱动     |
|      | $host           | string  | localhost | 主机名         |
|      | $port           | integer | 3306      | 端口号         |
|      | $user           | string  | root      | 用户名         |
|      | $password       | string  | root      | 密码           |
|      | $driver_options |         |           | 数据库驱动选项 |
|      | $db_name        | string  | mysql     | 数据库名       |
|      | $table_name     | string  |           | 数据表名       |
|      | $primary_key    | string  |           | 主键名         |
|      | $join           |         |           | 连接           |
|      | $group_by       |         |           | 分组           |
|      | $having         |         |           | 分组条件       |
|      | $return         |         |           | 返回类型选项   |
|      |                 |         |           |                |
|      |                 |         |           |                |
|      |                 |         |           |                |
|      |                 |         |           |                |
|      |                 |         |           |                |



## 公共方法

| #    | 方法                | 返回值  | 描述                 |
| ---- | ------------------- | ------- | -------------------- |
|      | __construct()       |         | 传入数据库配置       |
|      | setVar()            |         | 批量设置属性         |
|      | init()              |         | 初始化数据库         |
|      | _init()             |         | 自定义初始化         |
|      | getAdapter()        | object  | 获取适配器对象       |
|      | query()             |         | SQL 查询             |
|      | from()              | string  | 获取数据库和表名     |
|      | sqlColumns()        | string  | SQL 列名             |
|      | sqlSet()            | string  | SQL 设值             |
|      | sqlWhere()          | string  | SQL 条件             |
|      | insert()            |         | 插入数组数据         |
|      | into()              |         | 批量插入             |
|      | find()              |         | 查询单行             |
|      | sel()               |         | 更完善的查询单行     |
|      | count()             | integer | 计算总数             |
|      | pageCount()         | integer | 计算页码总数         |
|      | select()            |         | 查询多行             |
|      | _select()           |         | 更完善的查询多行     |
|      | update()            |         | 更新数据             |
|      | set()               | array   | 批量更新             |
|      | array_diff_kv()     | array   | 比较数组的键值       |
|      | fieldMessageQueue() |         | 放在字段里之后处理   |
|      | __call()            |         | 重载方法             |
|      | clearArrayByKey()   | array   | 清除数组的数字键名项 |
|      |                     |         |                      |

