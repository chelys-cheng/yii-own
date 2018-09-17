<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 16:52
 */

// 因为还没有运行自动加载，所以此处是手动加载
require __DIR__ . '/BaseYii.php';

class Yii extends \yii\BaseYii
{

}

// 通过Yii的类级别方法`autoload`方法，实现自动加载功能，且后加载的类会放在队列的前端
spl_autoload_register(['Yii', 'autoload'], true, true);
// 获取Yii类的类文件地址
Yii::$classMap = require __DIR__ . '/classes.php';
// 上面已经实现了自动加载类的方法，所以此处可以开始利用类的自动加载来实例化了
//Yii::$container = new yii\di\Container();