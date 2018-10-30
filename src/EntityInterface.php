<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 30.10.2018
 * Time: 19:52
 */

namespace GlobalTS\Slugger;


use DateTimeInterface;

/**
 * Interface EntityInterface
 * @package GlobalTS\Slugger
 */
interface EntityInterface
{
    /**
     * @return int
     */
    public function getId(): int;
    
    /**
     * @return string
     */
    public function getHash(): string;
    
    /**
     * @return string
     */
    public function getTitle(): string;
    
    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface;
    
    
}
