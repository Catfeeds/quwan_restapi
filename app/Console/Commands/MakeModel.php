<?php
/**
 * 创建模型文件
 *
 * @package     App\Console\Commands
 * @author      张光强 <zhangguangqiang@vpgame.cn>
 * @version     v1.0 2016/11/23 15:37:46
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * 创建 Repositories 目录
 *
 * @package App\Console\Commands
 */
class MakeModel extends Command
{
    /**
     * 指令名称
     * @var string
     */
    protected $signature = 'make:model {name}';

    /**
     * 指令描述
     * @var string
     */
    protected $description = '创建模型文件';

    /**
     * 创建文件夹与文件
     */
    public function handle()
    {
        $name = $this->argument('name');

        if (!$name) {
            die('缺少表名[不用带前缀]');
        }

        $id = '';
        $intArr = [];
        $results = DB::select('SHOW FULL COLUMNS FROM qw_' . $name);
        if ($results) {
            foreach ($results as $key => $value) {
                if ((int)$key === 0) {
                    $id = $value->Field;
                }

                if (substr_count($value->Type, 'int')
                    || substr_count($value->Type, 'tinyint')
                    || substr_count($value->Type, 'smallint')
                    || substr_count($value->Type, 'mediumint')
                    || substr_count($value->Type, 'bigint')
                ) {
                    $intArr[$value->Field] = 'int';

                }
            }
        }

        $intArr = var_export($intArr, TRUE);

        $qz_name = $name;

        $name = convert_underline($name);

        $dir = base_path() . '/app/Models';

        $content = $this->template();

        $healthy = array('@name@', '@qz_name@', '@id@', '@arr@');
        $yummy = array($name, $qz_name, $id, $intArr);
        $content = str_replace($healthy, $yummy, $content);

        $file = $dir . '/' . $name . '.php';

        file_put_contents($file, $content);

        echo '创建成功' . PHP_EOL;

    }

    /**
     * 模板内容
     *
     * @return string
     */
    private function template()
    {
        $content = <<<EOD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class @name@ extends Model
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected \$table = '@qz_name@';

    /**
     * 黑名单，包含不能被赋值的属性数组
     *
     * @var array
     */
    protected \$guarded = ['@id@'];

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public \$timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected \$casts = @arr@;

}

EOD;
        return $content;
    }
}
