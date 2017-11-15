<?php
/**
 * 创建app下Presenters目录
 * 同时会初始化,基类文件
 *
 * @package     App\Console\Commands
 * @author      张光强 <zhangguangqiang@vpgame.cn>
 * @version     v1.0 2016/11/23 15:37:46
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * 创建 Presenters 目录
 *
 * @package App\Console\Commands
 */
class MakePresenters extends Command
{
    /**
     * 指令名称
     *
     * @var string
     */
    protected $signature = 'make:presenter';

    /**
     * 指令描述
     *
     * @var string
     */
    protected $description = 'Create a presenters directory';

    /**
     * 创建文件夹与文件
     */
    public function handle()
    {
        $dir = base_path() . '/app/Presenters';

        if (mkdir($dir) &&
            !is_dir($dir))
        {
            echo '创建目录失败,请检查文件夹权限' . PHP_EOL;
        }

        $content = $this->template();

        $file = $dir . '/Presenter.php';

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

namespace App\Presenters;

/**
 * Presenter层基类
 * Class Presenter
 
 * @package App\Presenters
 */
class Presenter
{
    public function __construct()
    {
    
    }
}
EOD;
        return $content;
    }
}
