<?php

$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*/
namespace @{namespace};

use @{modelCommon};
/**
 * Class @{fileName}
 */
class @{fileName} extends @{commonModel}
{
	// 数据表名
	protected \$table = '@{tableName}';
	
	/**
	* 远程一对多或者远程一对一的参数提示
	* @param1 最终目标表
	* @param2 中间表
	* @param3 中间表外键 -- 对应当前 model 主键
	* @param4 最终目标表外键 -- 对应中间表主键
	* @param5 当前模型主键
	* @param6 中间表主键
	*/
	

}
CODE;
