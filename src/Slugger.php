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
    public const KEY_DATE  = 'date';
    public const KEY_HASH  = 'hash';
    
    private const RULES = [
        self::KEY_ID    => "[0-9]+",
        self::KEY_HASH  => "[a-fA-F0-9]{40}",
        self::KEY_DATE  => "\d\d\d\d\-\d\d\-\d\d",
        self::KEY_TITLE => "[a-z0-9_-]+",
    ];
    
    /**
     * @var array
     */
    private $rules_keys;
    
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
        $this->rules_keys     = array_keys(self::RULES);
        
        $this->setRuleIfExists($rule);
    }
    
    /**
     * Returns available rules keys
     * @return array
     */
    public function getRulesKeys(): array
    {
        return $this->rules_keys;
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
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }
    
    /**
     * @return bool
     */
    public function hasRule()
    {
        return ! empty($this->rule);
    }
    
    /**
     * @param string $rule
     * @return Slugger
     */
    public function setRule(string $rule)
    {
        $this->rule    = trim($rule, " /");
        $this->pattern = $this->buildPattern($this->rule);
        return $this;
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
     * @param EntityInterface $entity
     * @param null|string $rule
     * @return string
     * @throws Exception
     */
    public function generateFromEntity(EntityInterface $entity, ?string $rule = null)
    {
        return $this->generate(
            $entity->getId(),
            $entity->getHash(),
            $entity->getCreatedAt()->format('Y-m-d'),
            $entity->getTitle(),
            $rule
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
    public function generate(int $id, string $hash, string $date, string $title, ?string $rule = null)
    {
        $this->setRuleIfExists($rule);
    
        if (! $this->hasRule()) {
            throw new Exception('Rule is empty - you must set rule parameter before processing', Exception::CODE_RULE_EMPTY);
        }
        $slug = str_replace($this->rules_keys, [
            $id,
            $hash,
            $date,
            $this->transliterator->slugify($title),
        ], $this->rule);
        
        return $slug;
    }
    
    /**
     * @param string $slug
     * @param string|null $rule
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
                return in_array($key, $this->rules_keys, true);
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
