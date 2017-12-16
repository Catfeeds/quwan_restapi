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

* 依赖服务：Mysql、Redis、Supervisor

### 系统架构说明

* 系统分层结构：MRSCP（Model+Repository+Service+Controller+Presenter）。

* 帮助助手(app/helpers.php)，在 `composer.json` 配置自动加载。

```
"autoload": {
    "files": [
        "app/helpers.php"
    ]
}
```

### 初始化说明
2. 安装 `vendor` 组件，`composer install --no-dev`（本地开发时，去掉 `--no-dev` 参数）。
3. 修改 `.env` 文件的相关环境配置信息。
5. 数据库连接标识，命名约定：`db_` + `database name`。



### 队列启动
`php artisan queue:work --tries=1`

### 自动取消订单
`https://restapi.qu666.cn/quwan/auto_order_cancel`
