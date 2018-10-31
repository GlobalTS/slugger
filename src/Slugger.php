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
    public const KEY_ID    = 'id';
    public const KEY_TITLE = 'title';
    public const KEY_HASH  = 'hash';
    public const KEY_DATE  = 'date';
    
    private const RULES = [
        self::KEY_ID    => "[0-9]+",
        self::KEY_HASH  => "[a-fA-F0-9]{40}",
        self::KEY_TITLE => "[a-z0-9_-]+",
        self::KEY_DATE  => "\d\d\d\d\-\d\d\-\d\d",
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
     */
    public function __construct(TransliteratorInterface $transliterator, string $rule = null)
    {
        $this->transliterator = $transliterator;
        $this->keys           = array_keys(self::RULES);
    
        $this->setRuleIfExists($rule);
    }
    
    /**
     * @param string $rule
     */
    public function setRule(string $rule)
    {
        $this->rule    = trim($rule, " /");
        $this->pattern = $this->buildPattern($this->rule);
    }
    
    /**
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
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
     * @return TransliteratorInterface
     */
    public function getTransliterator()
    {
        return $this->transliterator;
    }
    
    /**
     * @param EntityInterface $entity
     * @param null|string $rule
     * @return string
     * @throws Exception
     */
    public function generateFromEntity(EntityInterface $entity, ?string $rule = null)
    {
        $this->setRuleIfExists($rule);
        
        return $this->generate(
            $entity->getId(),
            $entity->getHash(),
            $entity->getTitle(),
            $entity->getCreatedAt()->format('Y-m-d')
        );
    }
    
    /**
     * @param int $id
     * @param string $hash
     * @param string $title
     * @param string $date
     * @param null|string $rule
     * @return string
     * @throws Exception
     */
    public function generate(int $id, string $hash, string $title, string $date, ?string $rule = null)
    {
        $this->setRuleIfExists($rule);
    
        if (empty($this->rule)) {
            throw new Exception('Rule is empty - you must set rule parameter before processing', Exception::CODE_RULE_EMPTY);
        }
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
     * @param null|string $rule
     * @return Result
     * @throws Exception
     */
    public function parse(string $slug, ?string $rule = null)
    {
        $this->setRuleIfExists($rule);
    
        if (empty($this->pattern)) {
            throw new Exception('pattern is empty - you must set rule parameter before processing', Exception::CODE_PATTERN_EMPTY);
        }
        
        $res = preg_match($this->pattern, $slug, $matches);
        
        if ($res) {
            
            $data = array_filter($matches, function ($key) {
                return in_array($key, $this->keys, true);
            }, ARRAY_FILTER_USE_KEY);
            
            return Result::fromArray($data);
            
        } else {
            throw new Exception('incorrect data', Exception::CODE_INCORRECT_DATA);
        }
        
    }
    
    /**
     * @param null|string $rule
     */
    private function setRuleIfExists(?string $rule)
    {
        if ($rule) {
            $this->setRule($rule);
        }
    }
    
}
