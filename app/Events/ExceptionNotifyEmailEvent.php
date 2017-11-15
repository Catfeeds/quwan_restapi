<?php
/**
 * 异常日志邮件事件操作
 *
 * @package     App\Events
 * @author      张光强 <zhangguangqiang@vpgame.cn>
 * @version     v1.0 2016/11/23 17:23
 */

namespace App\Events;

/**
 * 异常日志邮件事件操作
 *
 * @author 张光强 <zhangguangqiang@vpgame.cn>
 */
class ExceptionNotifyEmailEvent extends Event
{
    /**
     * @var array 异常信息
     */
    public $exception = null;

    /**
     * ExceptionNotifyEmailEvent constructor.
     *
     * @param \Exception $e 异常对象
     */
    public function __construct(\Exception $e)
    {
        $this->exception = $e;
    }
}
