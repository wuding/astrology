# Taurusphp



## 核心类

[Astrology\Start](Start.md) 导入配置，连接缓存，初始化路由，加载控制器，Composer 类加载规则添加

[Astrology\Route](Route.md) 请求路径参数查询，扫描模块控制器，模块控制器动作参数名称及修正，键名合并数组

[Astrology\Controller](Controller.md) （数组）变量值获取及过滤，配置项，获取动作方法，开启会话和视图

[Astrology\Database](Database.md) 导入配置，数据库适配器，SQL 语句，增查改删（及批量），数量页码，数组比较合并清除



## 扩展类

DOM 初始化，去掉标签、属性、内容，获取内部 HTML

Filesystem 检测、创建目录，读写文件

Mbstring 转换编码，正则替换

PhpCurl 初始化，请求方式和数据，模拟浏览器下载、获取头信息，执行预定义

PhpMemcache 连接，设置获取检测

PhpPdo 初始化，预处理语句，插入和查询，方法重载

PhpPdoMysql 数据源名称，连接实例

SimpleXML 初始化，获取元素对象

Zlib 解压 gzip 文件



## 插件类

Robot 初始化，属性配置，下载、解析、绑定、优化列表和条目及分类，解析 JSON 和 XML，读写文件

[Fang](Fang.md) 自定义初始化，下载、解析出租和地区列表及详情，更新状态，解析 DOM



## 数据表模型

RentingSiteArea 查找、检测省，检测行（城市、区县、乡镇、小区）

RentingSiteDetail 检测行，获取多行