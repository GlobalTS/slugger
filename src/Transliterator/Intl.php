<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 30.10.2018
 * Time: 19:49
 */

namespace GlobalTS\Slugger\Transliterator;


use GlobalTS\Slugger\TransliteratorInterface;

/**
 * Class Intl
 * @package GlobalTS\Slugger\Transliterator
 */
class Intl implements TransliteratorInterface
{
    /**
     * @param string $string
     * @return string
     */
    public function slugify(string $string)
    {
        $string = $this->transliterate($string);
        $string = preg_replace('/[-\s]+/', '-', $string);
        $string = preg_replace('/[^a-z0-9\._-]+/', '', $string);
        return trim($string, '-');
    }
    
    /**
     * @param string $string
     * @return string
     */
    public function transliterate(string $string)
    {
        return transliterator_transliterate(
            "Any-Latin; Latin-ASCII; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();",
            $string
        );
    }
}
