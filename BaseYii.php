<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 16:36
 */

namespace yii;

use yii\base\InvalidArgumentException;
use yii\base\UnknownClassException;

defined('YII_BEGIN_TIME') or define('YII_BEGIN_TIME', microtime(true));
defined('YII2_PATH') or define('YII2_PATH', __DIR__);
defined('YII_DEBUG') or define('YII_DEBUG', false);


class BaseYii
{
    /**
     * @var array 存放类的路径信息
     */
    public static $classMap = [];

    /**
     * @var array 存放别名的路径信息
     */
    public static $aliases = ['@yii' => __DIR__];

    public static function setAlias($alias, $path)
    {
        if (strncmp($alias, '@', 1)) {
            $alias = '@' . $alias;
        }
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if ($path !== null) {
            $path = strncmp($path, '@', 1) ? rtrim($path, '\\/') : static::getAlias($path);


            // 别名数组结构，以`@root`[表示根目录]为键名
            // 如果路径是表示根目录，则直接以["@root"=>$path]的形式存在
            // 如果路径中，除了根目录以外，还有其他的二级目录，则路径以数组存在，
            // 原先的根目录路径仅作为一个同键名元素
            // 如果存在的话，直接在根目录下添加一个路径即可
            if (!isset(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [$alias => $path];
                }
            } elseif (is_string(static::$aliases[$root])) {
                if ($pos === false) {
                    static::$aliases[$root] = $path;
                } else {
                    static::$aliases[$root] = [
                        $root => static::$aliases[$root],
                        $alias => $path
                    ];
                }
            } else {
                static::$aliases[$root][$alias] = $path;
                // 使用`krsort()`，把数组按键值进行逆向排序，这样子做可以有效确保长的别名会放在短的类以别名前面
                krsort(static::$aliases[$root]);
            }
        } elseif (isset(static::$aliases[$root])) {
            // 如果原先存在别名路径，赋值过来的值又是null
            // 则释放掉这个路径的内容
            if (is_array(static::$aliases[$root])) {
                unset(static::$aliases[$root][$path]);
            } elseif ($pos === false) {
                unset(static::$aliases[$root]);
            }
        }
    }

    /**
     * 通过别名获取到真实路径
     * 别名以`@`开头
     *
     * @param $alias
     * @param bool $throwException
     * @return mixed
     */
    public static function getAlias($alias, $throwException = true)
    {
        // strncmp进行比较的时候，如果相等，会等于0，不相等，便不等于0
        if (strncmp($alias, '@', 1)) {
            // 如果比较结果不等于0，则说明格式不正确
            return $alias;
        }

        // 如果有第二层路径['/'标识]，则将第一层作为根目录，否则直接作为根目录
        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);
        // `$root`代表了`@`开头的别名路径
        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                // 如果匹配到根目录为一个字符串，则在这字符串后面添加上后续的路径
                return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
            }
            // 如果匹配到的根目录不是字符串，则遍历所有的项
            foreach (static::$aliases[$root] as $name => $path) {
                // 匹配到符合的项，则将不属于别名的部分，拼进真实路径
                // 因为是从长的路径，匹配到短的路径，所以第一次匹配到的路径，即需要寻到的别名路径
                if (strpos($alias . '/', $name . '/') === 0) {
                    return $path . substr($alias, strlen($name));
                }
            }
        }

        if ($throwException) {
            throw new InvalidArgumentException("Invalid path alias: $alias");
        }

        return false;
    }

    /**
     * 类自动加载器
     *
     * @param string $className
     */
    public static function autoload($className)
    {
        // 直接从类路径信息数组里面调用类路径
        if (isset(static::$classMap[$className])) {
            $classFile = static::$classMap[$className];
            if ($classFile[0] === '@') {
                $classFile = static::getAlias($classFile);
            }
        } elseif (strpos($className, '\\') !== false) {
            $classFile = static::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
            if ($classFile === false || !is_file($classFile)) {
                return;
            }
        } else {
            return;
        }

        include $classFile;

        if (YII_DEBUG && !class_exists($className, false) && !interface_exists($className, false) && !trait_exists($className, false)) {
            throw new UnknownClassException("Unable to find '$className' in file: $classFile. Namespace missing?");
        }
    }
}