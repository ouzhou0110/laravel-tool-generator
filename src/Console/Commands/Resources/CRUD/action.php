<?php

$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*/
namespace @{namespace};

use @{modelPackage};
use Illuminate\Database\Eloquent\Builder;
use @{modelCommon};

/**
 * 用于数据库的各种 CUD(写、改、删) 操作
 * Class @{fileName}
 */
class @{fileName} extends @{commonModel}
{

    /**
     * @var @{modelName}|Builder
     * 模型
     */
    protected \$model;

    /**
     * @return string 可以实例化的模型类名
     */
    protected function model(): string
    {
        return @{modelName}::class;
    }
    
}
CODE;
