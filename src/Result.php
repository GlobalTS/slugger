<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 30.10.2018
 * Time: 19:43
 */

namespace GlobalTS\Slugger;

/**
 * Class Result
 * @package GlobalTS\Slugger
 */
class Result
{
    /**
     * @var int
     */
    private $id;
    
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var string
     */
    private $hash;
    
    /**
     * @var string
     */
    private $date;
    
    /**
     * Result constructor.
     * @param int $id
     * @param string|null $title
     * @param string|null $hash
     * @param string|null $date
     */
    public function __construct(?int $id = null, ?string $title = null, ?string $hash = null, ?string $date = null)
    {
        $this->id    = $id;
        $this->title = $title;
        $this->hash  = $hash;
        $this->date  = $date;
    }
    
    /**
     * @param array $data
     * @return Result
     */
    public static function fromArray(array $data)
    {
        return new static(
            $data['id'] ?? null,
            $data['title'] ?? null,
            $data['hash'] ?? null,
            $data['date'] ?? null
        );
    }
    
    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    /**
     * @return string|null
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }
    
    /**
     * @return string|null
     */
    public function getDate(): ?string
    {
        return $this->date;
    }
}
