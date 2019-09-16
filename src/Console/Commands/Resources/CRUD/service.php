<?php
$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*/
namespace @{namespace};

// 引入当前模型的CRUD操作
use @{actionPackage};
use @{repositoryPackage};
use @{commonServicePackage};

/**
 * service 层，处理业务逻辑
 * Class @{fileName};
 */
class @{fileName} extends @{commonService}
{

	// CUD 操作
	private \$@{action};
	// R操作
	private \$@{repository};
	
	// 注入CRUD操作
	public function __construct()
	{
		\$this->@{action} = app()->make(@{actionClass}::class);
		\$this->@{repository} = app()->make(@{repositoryClass}::class);
	}
 
}
CODE;

