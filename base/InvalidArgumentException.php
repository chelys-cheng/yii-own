<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/17
 * Time: 11:35
 */

namespace yii\base;


class InvalidArgumentException extends InvalidParamException
{
    public function getName()
    {
        return 'Invalid Argument';
    }
}