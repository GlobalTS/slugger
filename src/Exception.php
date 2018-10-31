<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 30.10.2018
 * Time: 19:46
 */

namespace GlobalTS\Slugger;

/**
 * Class Exception
 * @package GlobalTS\Slugger
 */
class Exception extends \Exception
{
    const CODE_INCORRECT_DATA = 1;
    const CODE_RULE_EMPTY     = 2;
    const CODE_PATTERN_EMPTY  = 3;
    
}
