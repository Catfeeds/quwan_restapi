<?php

$allowEnv = ['develop', 'testing', 'production'];

if ($argc < 2) {
    echo '参数错误。';
    exit(1);
}

$envName = strtolower($argv[1]);

if (!in_array($envName, $allowEnv, true)) {
    echo '只允许[develop, testing, production]参数错误。';
    exit(1);
}

echo false === compile($envName)
    ? '环境配置编译失败。'
    : '环境配置编译成功。';

echo "\n";

/**
 * 从 zookeeper 获取配置信息
 *
 * @return array
 */
function get_zookeeper_config()
{
    $filePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'zookeeper-config.json';

    if (!is_readable($filePath) ||
        !is_file($filePath))
    {
        echo sprintf('Unable to read the environment file at %s.', $filePath);
        exit(1);
    }

    $config = file_get_contents($filePath);

    $configs = (array)json_decode($config, true);

    // 只要一个节点获取失败，就要终止编译
    if (empty($configs)) {
        echo 'the environment file is empty';
        exit(1);
    }

    $data = [];
    foreach ($configs as $key => $config) {

        $data[$key] = json_decode($config, true);
    }

    return $data;
}

/**
 * 设置别名
 * 解决 zookeeper 配置key 与 env 配置key不一致
 *
 * @return array
 */
function get_alias()
{
    $aliases = [
        '/config/mysqldb/vpgame' => [
            'username' => 'DB_USERNAME',
            'password' => 'DB_PASSWORD',
            'host'     => 'DB_HOST',
            'dbname'   => 'DB_DATABASE',
            'port'     => 'DB_PORT'
        ],

        '/config/mysqldb/logs' => [
            'username' => 'DB_LOGS_USERNAME',
            'password' => 'DB_LOGS_PASSWORD',
            'host'     => 'DB_LOGS_HOST',
            'dbname'   => 'DB_LOGS_DATABASE',
            'port'     => 'DB_LOGS_PORT'
        ],

        '/config/redis' => [
            'host'     => 'REDIS_HOST',
            'port'     => 'REDIS_PORT',
            'cluster'  => 'REDIS_CLUSTER',
            'password' => 'REDIS_PASSWORD'
        ],

        '/config/memcache' => [
            'host' => 'MEMCACHE_HOST',
            'port' => 'MEMCACHE_PORT'
        ]
    ];

    return $aliases;
}

/**
 * 获取应用配置信息
 *
 * @param  string $env
 *
 * @return array
 */
function get_app_config($env)
{
    $config = [
        'develop' => [
            'app_debug'   => 'true',
            'app_env'     => 'develop',
            'mail_enable' => 'false'
        ],
        'testing' => [
            'app_debug'   => 'false',
            'app_env'     => 'testing',
            'mail_enable' => 'false'
        ],
        'production' => [
            'app_debug'   => 'false',
            'app_env'     => 'production',
            'mail_enable' => 'true'
        ]
    ];

    return empty($env)
        ? $config['production']
        : $config[$env];
}

/**
 * 编译 APP 配置
 *
 * @param string $content 环境配置内容
 * @param string $env     环境名
 *
 * @return string
 */
function replace_app_env($content, $env)
{
    $patterns = [];
    $replaces = [];

    $config = get_app_config($env);

    foreach($config as $key => $value) {
        $key = strtoupper($key);

        $patterns[] = sprintf('/%s.*/', $key);
        $replaces[] = sprintf('%s=%s', $key, $value);
    }

    if (!empty($patterns) &&
        !empty($replaces))
    {
        $content = preg_replace($patterns, $replaces, $content);
    }

    return $content;
}

/**
 * 获取远程配置信息并处理替换
 *
 * @param $content
 *
 * @return mixed
 */
function replace_env($content)
{
    //获取配置标签
    preg_match_all('#\[(.*?)\]#', $content, $envTag);
    if (empty($envTag[1])) {
        echo '获取配置标签不存在！';
        exit(1);
    }

    $tags = (array)$envTag[1];

    $zkConfig = get_zookeeper_config();
    $aliases  = get_alias();

    $patterns = [];
    $replaces = [];
    foreach ($tags as $tag) {

        if (empty($zkConfig[$tag])) {
            continue;
        }

        $config = (array)$zkConfig[$tag];

        foreach ($config as $key => $value) {
            // 优先取别名
            if (!empty($aliases[$tag][$key])) {
                $key = $aliases[$tag][$key];
            }

            $key = strtoupper($key);
            $patterns[] = sprintf('/%s.*/', $key);
            $replaces[] = sprintf('%s=%s', $key, $value);
        }
    }

    if (!empty($patterns) &&
        !empty($replaces))
    {
        $content = preg_replace($patterns, $replaces, $content);
    }

    return $content;
}

/**
 * 编译开发环境配置
 *
 * @param string  $env 环境名称
 *
 * @return string
 */
function compile($env)
{
    $envTpFilePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env.tp';
    $envFilePath   = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';

    $content = file_get_contents($envTpFilePath);

    if (empty($content)) {
        echo '环境配置文件不存在！';
        exit(1);
    }

    $content = replace_app_env($content, $env);
    $content = replace_env($content);
    $content = preg_replace('#\[(.*?)\]#', '', $content);

    $result  = file_put_contents($envFilePath, $content);

    return $result;
}