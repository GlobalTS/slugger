<?php
/**
 * Created by PhpStorm.
 * User: topot
 * Date: 30.10.2018
 * Time: 20:04
 */

namespace GlobalTS\Slugger\Tests\Unit;

use GlobalTS\Slugger\EntityInterface;
use GlobalTS\Slugger\Exception as SluggerException;
use GlobalTS\Slugger\Result;
use GlobalTS\Slugger\Slugger;
use GlobalTS\Slugger\Transliterator\Intl as IntlTransliterator;
use PHPUnit\Framework\TestCase;

/**
 * Class SluggerTest
 * @package GlobalTS\Slugger\Tests\Unit
 */
class SluggerTest extends TestCase
{
    /**
     * @var IntlTransliterator
     */
    private $transliterator;
    
    /**
     *
     */
    protected function setUp()
    {
        $this->transliterator = new IntlTransliterator();
    }
    
    /**
     * @return array
     */
    public function getTestData()
    {
        return [
            'test_1' => [
                'rule'    => '/date/hash/title.id.html',
                'pattern' => "|^(?'date'\d\d\d\d\-\d\d\-\d\d)/(?'hash'[a-fA-F0-9]{40})/(?'title'[a-z0-9_-]+)\.(?'id'[0-9]+)\.html$|",
                'slug'    => '2018-06-29/fcf91c2c99f829e749bb62cb4ab0aabe5371d573/xiaomi-provela-ipo-po-niznej-granice-privlekla-472-mlrd.318.html',
                'id'      => 318,
                'hash'    => 'fcf91c2c99f829e749bb62cb4ab0aabe5371d573',
                'title'   => 'Xiaomi провела IPO по нижней границе, привлекла $4,72 млрд',
                'date'    => '2018-06-29',
            ],
            'test_2' => [
                'rule'    => 'id--title',
                'pattern' => "|^(?'id'[0-9]+)--(?'title'[a-z0-9_-]+)$|",
                'slug'    => '318--xiaomi-provela-ipo-po-niznej-granice-privlekla-472-mlrd',
                'id'      => 318,
                'hash'    => '',
                'title'   => 'Xiaomi провела IPO по нижней границе, привлекла $4,72 млрд',
                'date'    => '',
            ],
            'test_3' => [
                'rule'    => 'hash',
                'pattern' => "|^(?'hash'[a-fA-F0-9]{40})$|",
                'slug'    => 'fcf91c2c99f829e749bb62cb4ab0aabe5371d573',
                'id'      => null,
                'hash'    => 'fcf91c2c99f829e749bb62cb4ab0aabe5371d573',
                'title'   => '',
                'date'    => '',
            ],
            'test_4' => [
                'rule'    => 'id',
                'pattern' => "|^(?'id'[0-9]+)$|",
                'slug'    => '318',
                'id'      => 318,
                'hash'    => '',
                'title'   => '',
                'date'    => '',
            ],
            'test_5' => [
                'rule'    => 'hash.id',
                'pattern' => "|^(?'hash'[a-fA-F0-9]{40})\.(?'id'[0-9]+)$|",
                'slug'    => 'fcf91c2c99f829e749bb62cb4ab0aabe5371d573.318',
                'id'      => 318,
                'hash'    => 'fcf91c2c99f829e749bb62cb4ab0aabe5371d573',
                'title'   => '',
                'date'    => '',
            ],
        ];
    }
    
    /**
     * @dataProvider getTestData
     * @param string $rule
     * @param string $pattern
     * @param string $slug
     * @param int $id
     * @param string $title
     * @param string $hash
     * @param string $date
     * @throws SluggerException
     */
    public function testParse(string $rule, string $pattern, string $slug, ?int $id, ?string $hash, ?string $title, ?string $date)
    {
        $slugger  = new Slugger($this->transliterator, $rule);
        $expected = new Result($id, $this->transliterator->slugify($title), $hash, $date);
        $actual   = $slugger->parse($slug);
        $this->assertEquals($expected, $actual);
        $this->assertSame($pattern, $slugger->getPattern());
    }
    
    /**
     * @dataProvider getTestData
     * @param string $rule
     * @param string $pattern
     * @param string $slug
     * @param int $id
     * @param string $title
     * @param string $hash
     * @param string $date
     * @throws SluggerException
     */
    public function testParseWithRule(string $rule, string $pattern, string $slug, ?int $id, ?string $hash, ?string $title, ?string $date)
    {
        $slugger  = new Slugger($this->transliterator);
        $expected = new Result($id, $this->transliterator->slugify($title), $hash, $date);
        $actual   = $slugger->parse($slug, $rule);
        $this->assertEquals($expected, $actual);
        $this->assertSame($pattern, $slugger->getPattern());
    }
    
    /**
     * @dataProvider getTestData
     * @param string $rule
     * @param string $pattern
     * @param string $slug
     * @param int $id
     * @param string $title
     * @param string $hash
     * @param string $date
     * @throws SluggerException
     */
    public function testGenerateFromEntity(string $rule, string $pattern, string $slug, ?int $id, ?string $hash, ?string $title, ?string $date)
    {
        $entity  = $this->getEntityMock($id, $hash, $title, $date);
        $slugger = new Slugger($this->transliterator, $rule);
        $actual  = $slugger->generateFromEntity($entity);
        $this->assertSame($slug, $actual);
    }
    
    /**
     * @dataProvider getTestData
     * @param string $rule
     * @param string $pattern
     * @param string $slug
     * @param int $id
     * @param string $title
     * @param string $hash
     * @param string $date
     * @throws SluggerException
     */
    public function testGenerateFromEntityWithRule(string $rule, string $pattern, string $slug, ?int $id, ?string $hash, ?string $title, ?string $date)
    {
        $entity  = $this->getEntityMock($id, $hash, $title, $date);
        $slugger = new Slugger($this->transliterator, $rule);
        $actual  = $slugger->generateFromEntity($entity, $rule);
        $this->assertSame($slug, $actual);
    }
    
    /**
     * @param int|null $id
     * @param null|string $hash
     * @param null|string $title
     * @param null|string $date
     * @return EntityInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getEntityMock(?int $id, ?string $hash, ?string $title, ?string $date)
    {
        $entity = $this->createMock(EntityInterface::class);
        $entity->method('getId')->willReturn((int)$id);
        $entity->method('getTitle')->willReturn((string)$title);
        $entity->method('getHash')->willReturn((string)$hash);
        $entity->method('getCreatedAt')->willReturn(new \DateTime((string)$date));
        return $entity;
    }
    
    /**
     * @throws SluggerException
     */
    public function testParseThrowsExceptionOnBadData()
    {
        $slugger = new Slugger($this->transliterator, 'id.hash');
        $slug    = '318.bad-hash-here';
        $this->expectException(SluggerException::class);
        $this->expectExceptionCode(SluggerException::CODE_INCORRECT_DATA);
        $slugger->parse($slug);
    }
    
    /**
     * @throws SluggerException
     */
    public function testParseThrowsExceptionOnRuleNotExists()
    {
        $slugger = new Slugger($this->transliterator);
        $slug    = '318.2018-10-31';
        $this->expectException(SluggerException::class);
        $this->expectExceptionCode(SluggerException::CODE_PATTERN_EMPTY);
        $slugger->parse($slug);
    }
    
    /**
     * @throws SluggerException
     */
    public function testGenerateThrowsExceptionOnRuleNotExists()
    {
        $slugger = new Slugger($this->transliterator);
        $this->expectException(SluggerException::class);
        $this->expectExceptionCode(SluggerException::CODE_RULE_EMPTY);
        $slugger->generate(318, '', '', '');
    }
}
