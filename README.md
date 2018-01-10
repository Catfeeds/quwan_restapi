##  基本系统

### 基础框架：[Lumen Framework](https://lumen.laravel.com/docs/5.3)

### 环境变量（系统默认）

> 在 `.env` 环境文件里，配置 `APP_ENV` 变量。

* production 生产环境
* testing    测试环境
* develop    开发环境

### 服务器要求

* PHP版本：>=7.0.14

* PHP扩展：openssl、mbstring、PDO、pdo_mysql。

* 依赖服务：Mysql、Redis、迅搜搜索引擎

### 系统架构说明

* 系统分层结构：MRSCP（Model+Repository+Service+Controller+Presenter）。

* 项目文档在doc目录。

* 项目文档在重要配置信息在.env文件。

* 帮助助手(app/helpers.php)，在 `composer.json` 配置自动加载。

* 迅搜搜索引擎
```$xslt
索引目录
   /usr/local/xunsearch/data

运行
   /usr/local/xunsearch/bin/xs-ctl.sh restart

监控
     sudo /usr/local/xunsearch/bin/xs-ctl.sh -b local start
```

### 初始化说明
2. 安装 `vendor` 组件，`composer install --no-dev`（本地开发时，去掉 `--no-dev` 参数）。
3. 修改 `.env` 文件的相关环境配置信息。
5. 数据库连接标识，命名约定：`db_` + `database name`。


### 自动取订单计划任务
` */1 * * * * /usr/bin/curl https://restapi.qu666.cn/quwan/auto_order_cancel #订单取消时间 `
