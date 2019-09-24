<?php

namespace OuZhou\LaravelToolGenerator\Console\Commands\Resources\Route;

class WebRouter extends CommonRouter
{
    /**
     * Function: webGenerator
     * Notes: web路由生成器
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-20  15:31
     * @param $config
     */
    public static function generator($config)
    {
        $filePath = base_path('routes/web.php');
        // 获取数据
        file_exists($filePath) && $data = file_get_contents($filePath);
        // 检测是否被重写
        if (false === strpos($data, self::WEB_KEY)) {
            // 加载初始化模板
            self::init(self::initBaseWebRoute());
        }
        // 注入子路由
        self::append($config);

    }

    /**
     * Function: init
     * Notes: 初始化web.php路由
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-23  9:29
     * @param string $model
     */
    private static function init(string $model)
    {
        // 替换相关信息
        $model = str_replace([
            '@{override}', // 替换注入标识
            '@{prefix}', // 替换路由前缀
            '@{namespace}', // 替换主命名空间
            '@{injectWay1}', // 替换注入标识
            '@{webSonInject}', // 子权限标识注入
            '@{endTag}', // 结束标记
        ], [
            self::WEB_KEY,
            'web',
            'Web',
            self::INJECT_WAY_1, // 一级路由的注入
            self::WEB_SON_CREATED, // 二级路由注入定位符
            self::END_TAG, // 结束标记
        ], $model);

        // 保存文件
        $path = base_path('routes/web.php');
        self::save($path, $model, 0, true);
    }

    /**
     * Function: append
     * Notes: 向模型中追加或者生成路由配置
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-23  9:32
     * @param $config 配置信息
     * @return bool|void
     */
    private static function append($config)
    {
//        var_dump($config);
        // 注入方式1：UserController 这样的 controller注入
        if ($config->level == 1) {
            return self::append1($config);
        }

        if ($config->level == 2) {
            // 注入方式2：Admin/UserController 这样的 controller注入
            return self::append2($config);
        }

        // 注入方式2：Admin/UserController 这样的 controller注入
        if ($config->firstPrefix === 'web' && $config->level == 3) {
            // 方式2注入 == 避免大小写错误--首字母统统小写
            $config->filePath = lcfirst($config->filePath);
            // 去掉web
            $config->filePath = str_replace('web/', '', $config->filePath);
            unset($config->realPath[0]);
            --$config->level;
            $config->firstPrefix = lcfirst($config->realPath[1]);
//			var_dump($config);
            return self::append2($config);
        }

        // 注入方式3: Admin/System/.../IndexController 这样的 controller注入--至少三级
        return self::append3($config);
    }

    /**
     * Function: append1
     * Notes:
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-23  10:38
     * @param $config
     * @return bool
     *
     * 例子：UserController Web/UserController
     */
    private static function append1($config): bool
    {
        // 1，检测是否被初始化了
        $path = base_path('routes/web.php');
        $data = file_get_contents($path);
        if (false === strpos($data, self::INJECT_WAY_1 . self::END_TAG)) {
            // 模型不存在或者被删除，放弃追加
            echo "Fail: $path 指定的标识已经被删除，无法执行操作。标识：" . self::INJECT_WAY_1 . PHP_EOL;
            return false;
        }

        // 正式追加
        // 1. 获取追加数据模型
        $model = self::level1Mode();
        // 2. 替换数据
        $tag = '@Joker/' . $config->controllerPrefix; // #@Joker/User%

        // 检测是否已经存在
        if (false !== strpos($data, $tag . self::END_TAG)) {
            echo "Fail: $path 标记为 => $tag 的路由已经存在";
            return false;
        }
        $model = str_replace([
            '@{tag}', // 自身唯一标识
            '@{injectWay1}', // 注入地点标识
            '@{routerName}', // 路由名称
            '@{routerController}', // 所关联控制器
            '@{endTag}',
        ], [
            $tag, // #@Joker/User
            self::INJECT_WAY_1, // 固定：#one@injectWay1-dc483e80a7a0bd9ef71d8cf973673924
            lcfirst($config->controllerPrefix), // user
            $config->fileName, // UserController
            self::END_TAG,
        ], $model);

        // 注入到 web.php 指定位置
        $data = str_replace(self::INJECT_WAY_1 . self::END_TAG, $model, $data);
        return self::save($path, $data, FILE_TEXT, true); // 重写文件
    }

    /**
     * Function: append2
     * Notes:
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-24  10:05
     * @param $config
     * @return bool|string
     *
     * 例子 Admin/UserController  Web/UserController
     */
    private static function append2($config)
    {
        // 检测第一级是不是 web
        if ($config->firstPrefix == 'web') {
            // 1. 是，调用 append1 表示为一级路由
            return self::append1($config);
        }
        // 2. 否，检测 对应的第一级文件是否存在: admin.php
        $path = base_path('routes/' . self::WEB_SON_BASE_PATH . $config->firstPrefix . '.php');
        if (!file_exists($path)) {
            // 文件不存在，调用初始化程序
            $tag = '@' . ucfirst($config->firstPrefix);
            $namespace = ucfirst($config->firstPrefix);
            $prefix = lcfirst($config->firstPrefix);
            $model = self::initLevel2RouteFile($tag, $namespace, $prefix);
        } else {
            $model = file_get_contents($path);
        }

        // 检测末级标识是否存在
//        $lastTag = '@' . $config->filePath . '/Joker/' . $config->controllerPrefix; // #@Admin/Joker/User
        $lastTag = '@' . $config->filePath . '/' . $config->controllerPrefix; // #@Admin/Joker/User
//		echo $lastTag;
//		return false;
        if (false !== strpos($model, $lastTag . self::END_TAG)) {
            // 想想应该不会出现，因为此时控制器肯定重复了
            echo "Danger: $path 标记为 => $lastTag 的路由已经存在";
            return false;
        }


        // 检测标识是否存在

//        $aimTag = '@' . $config->filePath . '/Joker'; // #@Admin/Joker
        $aimTag = '@' . $config->filePath; // #@Admin/Joker
        if (false === strpos($model, $aimTag . self::END_TAG)) {
            // 没有二级路由的group
            $injectTag = self::INJECT_WAY_2 . '@' . $config->filePath;
            $tag = $aimTag;
            $namespace = '';
            $prefix = '';
            $sonInject = self::INJECT_WAY_3;
            $level2 = self::initLevel2Group($injectTag, $tag, $namespace, $prefix, $sonInject);

            // 组装到 $model 中
            $model = str_replace($injectTag . self::END_TAG, $level2, $model);
        }

        // 注入末级路由
//		echo $model;
        $routerName = lcfirst($config->controllerPrefix); // user
        $injectTag = self::INJECT_WAY_3 . $aimTag; // #three@injectWay3-e10adc3949ba59abbe56e057f20f883e@Admin/User
        $aimTag = $lastTag; // #@Admin/Joker
        $controllerName = $config->fileName; // UserController
        $lastLevel = self::initLastLevelRoute($injectTag, $aimTag, $routerName, $controllerName);
//		echo $lastLevel;
        // 组装到 $model 中
        $model = str_replace($injectTag . self::END_TAG, $lastLevel, $model);
//		echo $model;
        // 保存
        self::save($path, $model, 0, true);
        // 注入路径到web.php中
        return self::injectLevel2FilePathToWeb(base_path('routes/web.php'), self::WEB_SON_BASE_PATH . $config->firstPrefix . '.php');

    }

    /**
     * Function: append3
     * Notes:
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-24  17:36
     *
     * @param $config
     *
     * 例子
     * Admin/System/.../UserController
     *
     * @return bool
     */
    private static function append3($config)
    {
        // 检测第一级是不是 web 是就去掉
        if ($config->firstPrefix == 'web') {
            // 去掉web
            $config->filePath = lcfirst($config->filePath);
            $config->filePath = str_replace('web/', '', $config->filePath);
            unset($config->realPath[0]);
            $config->firstPrefix = lcfirst($config->realPath[1]);
            --$config->level;
//            var_dump($config);
        }
//		return false;
        // 2. 否，检测 对应的第一级文件是否存在: admin.php
        $path = base_path('routes/' . self::WEB_SON_BASE_PATH . $config->firstPrefix . '.php');
        if (!file_exists($path)) {
            // 文件不存在，调用初始化程序
            $tag = '@' . ucfirst($config->firstPrefix);
			$namespace = ucfirst($config->firstPrefix);
			$prefix = lcfirst($config->firstPrefix);
            $model = self::initLevel2RouteFile($tag, $namespace, $prefix);
        } else {
            $model = file_get_contents($path);
        }
//		echo $model . PHP_EOL;

        // 检测末级标识是否存在
        $lastTag = '@' . $config->filePath . '/' . $config->controllerPrefix; // #@Admin/Joker/User
//		echo $lastTag;
        if (false !== strpos($model, $lastTag . self::END_TAG)) {
            // 想想应该不会出现，因为此时控制器肯定重复了
            echo "Danger: $path 标记为 => $lastTag 的路由已经存在";
            return false;
        }

        // 处理中间的group组
        $middleArr = array_slice($config->realPath, 1, count($config->realPath) - 2);
        krsort($middleArr);
//		var_dump($middleArr);
        $son = ''; // 临时储存group
        $isReplace = false; // 标识是否在group生成过程中就被替换了
        foreach ($middleArr as $k => $v) {
            // 当前tag
            $tag = '@' . implode('/', array_slice($config->realPath, 0, $k + 2));
//            echo $tag;
            if (false === strpos($model, $tag . self::END_TAG)) {
                $injectTag = self::INJECT_WAY_3 . '@' . implode('/', array_slice($config->realPath, 0, $k + 1));
//                $tag = $tag;
                $namespace = $v;
                $prefix = lcfirst($v);
                $sonInject = self::INJECT_WAY_3;
                $level2 = self::initLevel2Group($injectTag, $tag, $namespace, $prefix, $sonInject);
//		        echo $level2 . PHP_EOL;
                if ($son) {
                    $sonTag = self::INJECT_WAY_3 . $tag;
                    $son = str_replace($sonTag . self::END_TAG, $son, $level2);
                } else {
                    $son = $level2;
                }
            } else {
                // 找到了
                $isReplace = true;
                $sonTag = self::INJECT_WAY_3 . $tag;
                if ($son) {
                    $model = str_replace($sonTag . self::END_TAG, $son, $model);
                }
                break;
            }
        }
        if (!$isReplace) {
            // 从第一级开始替换：@admin% 开始
            $findTag = self::INJECT_WAY_2 . '@' . ucfirst($config->firstPrefix);
            if ($son) {
                $model = str_replace($findTag . self::END_TAG, $son, $model);
            }
        }
//		echo $son;
//		echo 'sssssssssssssssss' . PHP_EOL;
//		echo $model;

        // 注入末级路由
//		echo $model;
        $routerName = lcfirst($config->controllerPrefix); // user
        $injectTag = self::INJECT_WAY_3 . '@' . $config->filePath; // #three@injectWay3-e10adc3949ba59abbe56e057f20f883e@Admin/User
        $aimTag = $lastTag; // #@Admin/Joker
        $controllerName = $config->fileName; // UserController
        $lastLevel = self::initLastLevelRoute($injectTag, $aimTag, $routerName, $controllerName);
//		echo $lastLevel;
        // 组装到 $model 中
        $model = str_replace($injectTag . self::END_TAG, $lastLevel, $model);
//		echo $model;
        // 保存
        self::save($path, $model, 0, true);
//         注入路径到web.php中
        return self::injectLevel2FilePathToWeb(base_path('routes/web.php'), self::WEB_SON_BASE_PATH . $config->firstPrefix . '.php');
    }


}