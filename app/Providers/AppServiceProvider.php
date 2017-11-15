<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 打印查询日志。
        if(App::environment('develop', 'testing')) {

            $this->recordDatabaseQuery();
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * 生成数据库操作sql语句
     *
     * @param $query
     */
    private function makeSql($query)
    {
        $sqlFile  = base_path('script') . '/' . date('Y_m_d_Hi') . '.sql';

        if (false !== stripos($query->sql, 'create table')) {

            $dbName = config("database.connections.{$query->connectionName}.database");
            $sql = str_replace('create table ', "create table `{$dbName}`.", $query->sql);

            file_put_contents($sqlFile, "{$sql};\n\n", FILE_APPEND);
        }

        if (false !== stripos($query->sql, 'alter table')) {

            $dbName = config("database.connections.{$query->connectionName}.database");
            $sql = str_replace('alter table ', "alter table `{$dbName}`.", $query->sql);

            file_put_contents($sqlFile, "{$sql};\n\n", FILE_APPEND);
        }
    }

    /**
     * 记录数据库操作日志
     */
    private function recordDatabaseQuery()
    {
        DB::listen(function($query) {

            $yummy = [];
            if (false === empty($query->bindings)) {
                foreach ((array)$query->bindings as $key => $value) {
                    $yummy[] = is_string($value) ? "'" . $value . "'" : $value;
                }
            }

            $tmpArr = explode('?', $query->sql);

            $logSql = '';
            if (false === empty($tmpArr)) {
                $num = count($tmpArr)-1;
                foreach ((array)$tmpArr as $key => $value) {
                    $logSql .= $value;
                    if($key !== $num){
                        $logSql .= $yummy[$key] ?? '';
                    }
                }
            } else {
                $logSql = $query->sql;
            }


            Log::info('Database Query:', [
                'db'        => config("database.connections.{$query->connectionName}.database"),
                'sql'      => $logSql,
                'bindings' => $query->bindings,
                'time'     => $query->time
            ]);

            if (App::environment('develop')) {
                $this->makeSql($query);
            }
        });
    }
}