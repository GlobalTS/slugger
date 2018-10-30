<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 30.10.2018
 * Time: 19:42
 */

namespace GlobalTS\Slugger;

/**
 * Class Slugger
 * @package GlobalTS\Slugger
 */
class Slugger
{
    private const RULES = [
        'id'    => "[0-9]+",
        'hash'  => "[a-fA-F0-9]{40}",
        'title' => "[a-z0-9_-]+",
        'date'  => "\d\d\d\d\-\d\d\-\d\d",
    ];
    
    /**
     * @var array
     */
    private $keys;
    
    /**
     * @var TransliteratorInterface
     */
    private $transliterator;
    
    /**
     * @var string
     */
    private $rule;
    
    /**
     * @var string
     */
    private $pattern;
    
    /**
     * Slugger constructor.
     * @param TransliteratorInterface $transliterator
     * @param string $rule
     * @param string $pattern
     */
    public function __construct(TransliteratorInterface $transliterator, string $rule, string $pattern = null)
    {
        $this->transliterator = $transliterator;
        $this->rule           = trim($rule, " /");
        $this->keys           = array_keys(self::RULES);
        $this->pattern        = $pattern ?? $this->buildPattern($this->rule);
    }
    
    /**
     * @param string $rule
     * @return string
     */
    public function buildPattern(string $rule)
    {
        $pattern = quotemeta(addslashes($rule));
        foreach (self::RULES as $key => $value) {
            $pattern = str_replace($key, "(?'{$key}'{$value})", $pattern);
        }
        return "|^{$pattern}$|";
    }
    
    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }
    
    /**
     * @param EntityInterface $article
     * @return string
     */
    public function generateFromEntity(EntityInterface $article)
    {
        return $this->generate(
            $article->getId(),
            $article->getHash(),
            $article->getTitle(),
            $article->getCreatedAt()->format('Y-m-d')
        );
    }
    
    /**
     * @param int $id
     * @param string $hash
     * @param string $title
     * @param string $date
     * @return mixed
     */
    public function generate(int $id, string $hash, string $title, string $date)
    {
        $slug = str_replace($this->keys, [
            $id,
            $hash,
            $this->transliterator->slugify($title),
            $date,
        ], $this->rule);
        
        return $slug;
    }
    
    /**
     * @param string $slug
     * @return Result
     * @throws Exception
     */
    public function parse(string $slug)
    {
        $res = preg_match($this->pattern, $slug, $matches);
        
        if ($res) {
            
            $data = array_filter($matches, function ($key) {
                return in_array($key, $this->keys, true);
            }, ARRAY_FILTER_USE_KEY);
            
            return Result::fromArray($data);
            
        } else {
            throw new Exception('incorrect data');
        }
        
    }
    
}
