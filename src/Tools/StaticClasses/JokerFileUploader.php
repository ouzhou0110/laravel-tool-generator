<?php

namespace OuZhou\LaravelToolGenerator\Tools\StaticClasses;

use OuZhou\LaravelToolGenerator\Exceptions\FileNumOverflowException;
use OuZhou\LaravelToolGenerator\Exceptions\MakeDirFailException;
use OuZhou\LaravelToolGenerator\Exceptions\NotFoundFileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class JokerFileUploader
{
    // 基础路径
    const FILE_SAVE_DIR = '/uploads/';

    // 最大允许上传数量
    const MAX_NUMBER = 10;

    // 允许上传的扩展名 -- 图片
    const ALLOW_FILE_EXTENSION_IMAGE = [
        'jpg', 'jpeg', 'png', 'gif',
    ];

    // 允许上传的拓展命 -- 文件
    const ALLOW_FILE_EXTENSION = [
        'xls', 'doc', 'docs', 'txt', 'md', 'pdf',
    ];

    // 图片储存位置 -- 基础路径
    const IMAGE_BASE_PATH = 'images/';

    // 其他文件储存位置 -- 基础路径
    const OTHER_FILE_BASE_PATH = 'files/';

    /**
     * Function: images
     * Notes: 多张图片上传
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-25  上午10:07
     * @param array $files Request::file() 传递的数据
     * @param string $relativePath 图片相对路径：basePath:/uploads/images/
     * @param int $max
     * @param array $allowExtensions
     * @return array
     * @throws FileNumOverflowException
     * @throws MakeDirFailException
     * @throws NotFoundFileException
     */
    public static function images(array $files, string $relativePath = 'common', int $max = 1, array $allowExtensions = [])
    {
        $path = self::FILE_SAVE_DIR . self::IMAGE_BASE_PATH . $relativePath;
        return (new self())->baseUploader($files, $path, count($allowExtensions) ? $allowExtensions : self::ALLOW_FILE_EXTENSION_IMAGE, $max);

    }

    /**
     * Function: imageOnlySuccessPath
     * Notes: 单张图片上传
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-25  下午2:51
     * @param $file
     * @param string $relativePath
     * @param int $max
     * @param array $allowExtensions
     * @return mixed
     * @throws FileNumOverflowException
     * @throws MakeDirFailException
     * @throws NotFoundFileException
     */
    public static function image($file, string $relativePath = 'common', int $max = 1, array $allowExtensions = [])
    {
        $res = self::images([$file], $relativePath, $max, $allowExtensions);

        return (current($res['successList']) ?? false);
    }

    /**
     * Function: images
     * Notes:
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-25  上午10:07
     * @param array $files Request::file() 传递的数据
     * @param string $relativePath 图片相对路径：basePath:/uploads/images/
     * @param int $max
     * @param array $allowExtensions
     * @return array
     * @throws FileNumOverflowException
     * @throws MakeDirFailException
     * @throws NotFoundFileException
     */
    public static function files(array $files, string $relativePath = 'common', int $max = 1, array $allowExtensions = [])
    {
        $path = self::FILE_SAVE_DIR . self::OTHER_FILE_BASE_PATH . $relativePath;

        return (new self())->baseUploader($files, $path, count($allowExtensions) ? $allowExtensions : self::ALLOW_FILE_EXTENSION, $max);

    }

    /**
     * Function: imageOnlySuccessPath
     * Notes:
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-25  下午2:51
     * @param $file
     * @param string $relativePath
     * @param int $max
     * @param array $allowExtensions
     * @return mixed
     * @throws FileNumOverflowException
     * @throws MakeDirFailException
     * @throws NotFoundFileException
     */
    public static function file($file, string $relativePath = 'common', int $max = 1, array $allowExtensions = [])
    {
        $res = self::files([$file], $relativePath, $max, $allowExtensions);

        return (current($res['successList']) ?? false);
    }

    /**
     * Function: baseUploader
     * Notes: 上传文件的通用实现
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-25  下午2:58
     * @param array $files
     * @param string $path
     * @param array $allowExtension
     * @param int $max
     * @return array
     * @throws FileNumOverflowException
     * @throws MakeDirFailException
     * @throws NotFoundFileException
     * @throws \Exception
     */
    private function baseUploader(array $files, string $path, array $allowExtension, int $max = 1)
    {
        // 成功上传列表
        $successList = [];
        // 上传失败列表
        $failList = [];

        // 获取最大上传数
        $max = $max ?? self::MAX_NUMBER;

        // 验证是否超过最大值
        if (count($files) > $max) {
            throw new FileNumOverflowException();
        }

        // 获取完整的文件路径
        // 1. 拼接主路径
        // 2. 根据时间分目录 -- 2019/09/01
        $path .= date('Y/m/d', time());

        // 创建文件夹
        if (!self::makeDir($path)) {
            throw new MakeDirFailException();
        }

        // 上传文件
        foreach ($files as $file) {
            // 1. 检测类型是否正确
            if (!$file instanceof UploadedFile) {
                throw new NotFoundFileException();
            }
            // 2. 判断扩展名是否被允许
            // 获取后缀，并转化为小写
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowExtension)) {
                // 上传失败列表
                $failList[] = $file->getClientOriginalName();
            }
            // 3. 移动图片
            $saveName = random_bytes(30) . "." . $ext;
            $res = $file->move(public_path() . $path, $saveName);
            if (!$res) {
                $failList[] = $file->getClientOriginalName();
            }
            $successList[] = "$path/$saveName";
        }

        return [
            'successNum' => count($successList), // 成功数量
            'successList' => $successList, // 成功者的路径
            'failNum' => count($failList), // 失败数量
            'failList' => $failList, // 失败的文件名称
        ];
    }

    /**
     * Function: makeDir
     * Notes:
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-25  下午2:00
     * @param $dir
     * @return bool
     */
    private static function makeDir($dir)
    {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0777, true)) {
                return false;
            }
        }
        return $dir;
    }

    public static function test()
    {
        return 'sss';
    }

}
