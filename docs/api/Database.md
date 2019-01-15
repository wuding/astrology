# Class Astrology\Database



源码 https://github.com/wuding/astrology/blob/secret/src/Astrology/Database.php



## 公共属性

| #    | 属性            | 类型    | 默认值    | 描述           |
| ---- | --------------- | ------- | --------- | -------------- |
| 1    | $adapter        | object  |           | 适配器         |
| 2    | $driver         | string  | pdo_mysql | 数据库驱动     |
| 3    | $host           | string  | localhost | 主机名         |
| 4    | $port           | integer | 3306      | 端口号         |
| 5    | $user           | string  | root      | 用户名         |
| 6    | $password       | string  | root      | 密码           |
| 7    | $driver_options |         |           | 数据库驱动选项 |
| 8    | $db_name        | string  | mysql     | 数据库名       |
| 9    | $table_name     | string  |           | 数据表名       |
| 10   | $primary_key    | string  |           | 主键名         |
| 11   | $join           |         |           | 连接           |
| 12   | $group_by       |         |           | 分组           |
| 13   | $having         |         |           | 分组条件       |
| 14   | $return         |         |           | 返回类型选项   |



## 公共方法

| #    | 方法                | 返回值  | 描述                 |
| ---- | ------------------- | ------- | -------------------- |
| 1    | __construct()       |         | 传入数据库配置       |
| 2    | setVar()            |         | 批量设置属性         |
| 3    | init()              |         | 初始化数据库         |
| 4    | _init()             |         | 自定义初始化         |
| 5    | getAdapter()        | object  | 获取适配器对象       |
| 6    | query()             |         | SQL 查询             |
| 7    | from()              | string  | 获取数据库和表名     |
| 8    | sqlColumns()        | string  | SQL 列名             |
| 9    | sqlSet()            | string  | SQL 设值             |
| 10   | sqlWhere()          | string  | SQL 条件             |
| 11   | insert()            |         | 插入数组数据         |
| 12   | into()              |         | 批量插入             |
| 13   | find()              |         | 查询单行             |
| 14   | sel()               |         | 更完善的查询单行     |
| 15   | count()             | integer | 计算总数             |
| 16   | pageCount()         | integer | 计算页码总数         |
| 17   | select()            |         | 查询多行             |
| 18   | _select()           |         | 更完善的查询多行     |
| 19   | update()            |         | 更新数据             |
| 20   | set()               | array   | 批量更新             |
| 21   | array_diff_kv()     | array   | 比较数组的键值       |
| 22   | fieldMessageQueue() |         | 放在字段里之后处理   |
| 23   | __call()            |         | 重载方法             |
| 24   | clearArrayByKey()   | array   | 清除数组的数字键名项 |

