<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 30.10.2018
 * Time: 19:48
 */

namespace GlobalTS\Slugger;

/**
 * Interface TransliteratorInterface
 * @package GlobalTS\Slugger
 */
interface TransliteratorInterface
{
    /**
     * @param string $string
     * @return string
     */
    public function transliterate(string $string);
    
    /**
     * @param string $string
     * @return string
     */
    public function slugify(string $string);
}
