<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/17
 * Time: 11:36
 */

namespace yii\base;


class UnknownClassException extends \BadMethodCallException
{
    public function getName()
    {
        return 'Unknown Class';
    }
}