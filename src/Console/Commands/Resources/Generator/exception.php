<?php

$code = <<<CODE
<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*
*/
namespace @{namespace};

use Throwable;

class @{class} extends \Exception
{
    public function __construct(string \$message = "", int \$code = 0, Throwable \$previous = null)
    {
        empty(\$message) && \$message = '@{message}: ' . \$message;
        parent::__construct(\$message, \$code, \$previous);
    }
}

CODE;

