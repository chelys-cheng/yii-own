<?php
/**
 * 测试类自动加载
 * User: Administrator
 * Date: 2018/9/17
 * Time: 15:40
 */

/**
 * 如果去除加载Yii文件，则会出现`Class 'yii\base\UnknownClassException' not found`的错误
 */
require __DIR__ . '/../Yii.php';

use yii\base\UnknownClassException;

throw new UnknownClassException('测试错误类自动加载');