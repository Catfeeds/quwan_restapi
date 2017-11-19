<?php
/**
 * json web token 相关
 * 生成/撤销/验证等操作
 */

namespace App\Services;


class YanzhenService
{
    /**
     * 是否是utf-8
     * @param  [type]  $word [description]
     * @return boolean       [description]
     */
    public function is_utf8($word) {
        if (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $word) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}$/", $word) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $word) == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断字符和一维数组 0认为非空
     * @param  {[type]}  $value [str OR arr]
     * @return {Boolean}        [description]
     */
    public static function is_blank($value)
    {
        return empty($value) && !is_numeric($value);
    }


    /**
     * 对比字符串
     * @param  [type] $str1 [description]
     * @param  [type] $str2 [description]
     * @return [type]       [description]
     */
    public static function duiBi($str1,$str2)
    {
        $tag = true;
        if(trim($str1) !== trim($str2))
        {
            $tag = false;
        }
        return $tag;
    }


    /**
     * 密码强度检测  10为满分
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public static function qianDu($str)
    {
        $score = '';
        if(preg_match("/[0-9]+/",$str))
        {
            $score = '非常弱';
        }
        if(preg_match("/[0-9]{3,}/",$str))
        {
            $score = '很弱';
        }
        if(preg_match("/[a-z]+/",$str))
        {
            $score = '弱';
        }
        if(preg_match("/[a-z]{3,}/",$str))
        {
            $score = '一般';
        }
        if(preg_match("/[A-Z]+/",$str))
        {
            $score = '好';
        }
        if(preg_match("/[A-Z]{3,}/",$str))
        {
            $score = '很好';
        }
        if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/",$str))
        {
            $score = '非常好';
        }
        if(preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]{3,}/",$str))
        {
            $score = '超好';
        }
        if(strlen($str) >= 10)
        {
            $score = '最强密码组合';
        }

        return $score;
    }


    /**
     * 自定义正则验证
     * @param  [type] $preg [description]
     * @param  [type] $str  [description]
     * @return [type]       [description]
     */
    public static function checkOwn($preg, $str)
    {
        if (!$preg || !$str) {
            return false;
        }
        return preg_match($preg, $str) ? true : false;
    }




    /**
     * 匹配非负整数
     *
     * @param string $str    （正整数 + 0)
     * @return boolean
     */
    public static function isTrueNum($str) {
        if (!$str) {
            return false;
        }
        return preg_match('#^[0-9]*$#', $str) ? true : false;
    }

    /**
     * 正则小数格式
     *
     * @param string $str    格式如: 99 或 99.00 或 99.99
     * @return boolean
     */
    public static function isFloat($str) {
        if (!$str) {
            return false;
        }
        return preg_match('#^[1-9]\d*(\.\d*|0*)?$#', $str) ? true : false;
    }

    /**
     * 正则表达式验证email格式
     *
     * @param string $str    所要验证的邮箱地址
     * @return boolean
     */
    public static function isEmail($str) {
        if (!$str) {
            return false;
        }
        return preg_match('#[a-z0-9&\-_.]+@[\w\-_]+([\w\-.]+)?\.[\w\-]+#is', $str) ? true : false;
    }
    /**
     * 正则表达式验证网址
     *
     * @param string $str    所要验证的网址
     * @return boolean
     */
    public static function isUrl($str) {
        if (!$str) {
            return false;
        }
        return preg_match('#(http|https|ftp|ftps)://([\w-]+\.)+[\w-]+(/[\w-./?%&=]*)?#i', $str) ? true : false;
    }
    /**
     * 验证字符串中是否含有汉字
     *
     * @param integer $string    所要验证的字符串。注：字符串编码仅支持UTF-8
     * @return boolean
     */
    public static function isChineseCharacter($string) {
        if (!$string) {
            return false;
        }
        return preg_match('~[\x{4e00}-\x{9fa5}]+~u', $string) ? true : false;
    }
    /**
     * 验证字符串中是否含有非法字符
     *
     * @param string $string    待验证的字符串
     * @return boolean
     */
    public static function isInvalidStr($string) {
        if (!$string) {
            return false;
        }
        return preg_match('#[!#$%^&*(){}~`"\';:?+=<>/\[\]]+#', $string) ? true : false;
    }
    /**
     * 用正则表达式验证邮证编码
     *
     * @param integer $num    所要验证的邮政编码
     * @return boolean
     */
    public static function isPostNum($num) {
        if (!$num) {
            return false;
        }
        return preg_match('#^[1-9][0-9]{5}$#', $num) ? true : false;
    }
    /**
     * 正则表达式验证身份证号码
     *
     * @param integer $num    所要验证的身份证号码
     * @return boolean
     */
    public static function isPersonalCard($num) {
        if (!$num) {
            return false;
        }
        return preg_match('#^[\d]{15}$|^[\d]{18}$#', $num) ? true : false;
    }
    /**
     * 正则表达式验证IP地址, 注:仅限IPv4
     *
     * @param string $str    所要验证的IP地址
     * @return boolean
     */
    public static function isIp($str) {
        if (!$str) {
            return false;
        }
        if (!preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $str)) {
            return false;
        }
        $ipArray = explode('.', $str);
        //真实的ip地址每个数字不能大于255（0-255）
        return ($ipArray[0]<=255 && $ipArray[1]<=255 && $ipArray[2]<=255 && $ipArray[3]<=255) ? true : false;
    }
    /**
     * 用正则表达式验证出版物的ISBN号
     *
     * @param integer $str    所要验证的ISBN号,通常是由13位数字构成
     * @return boolean
     */
    public static function isBookIsbn($str) {
        if (!$str) {
            return false;
        }
        return preg_match('#^978[\d]{10}$|^978-[\d]{10}$#', $str) ? true : false;
    }

    /**
     * 用正则表达式验证电话号码(中国大陆区)
     * @param integer $num    所要验证的手机号
     * @return boolean
     */
    public static function isTel($num) {
        if (!$num) {
            return false;
        }
        return preg_match('/^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/', $num) ? true : false;
    }

    /**
     * 用正则表达式验证手机号码(中国大陆区)
     * @param integer $num    所要验证的手机号
     * @return boolean
     */
    public static function isMobile($num) {
        if (!$num) {
            return false;
        }
        return preg_match('/^(0)?(13[0-9]|15[0-9]|18[0-9])\d{8}$/i', $num) ? true : false;
    }
    /**
     * 检查字符串是否为空
     *
     * @access public
     * @param string $string 字符串内容
     * @return boolean
     */
    public static function isMust($string = null) {
        //参数分析
        if (is_null($string)) {
            return false;
        }
        return empty($string) ? false : true;
    }
    /**
     * 检查字符串长度
     *
     * @access public
     * @param string $string 字符串内容
     * @param integer $min 最小的字符串数
     * @param integer $max 最大的字符串数
     */
    public static function isLength($string = null, $min = 0, $max = 255) {
        //参数分析
        if (is_null($string)) {
            return false;
        }
        //获取字符串长度
        $length = strlen(trim($string));
        return (($length >= (int)$min) && ($length <= (int)$max)) ? true : false;
    }

}