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
     * @return array
     */
    public function getTestData()
    {
        return [
            'test_1' => [
                'id--title',
                "|^(?'id'[0-9]+)--(?'title'[a-z0-9_-]+)$|",
                '318--xiaomi-provela-ipo-po-niznej-granice-privlekla-472-mlrd',
                318,
                '',
                'Xiaomi провела IPO по нижней границе, привлекла $4,72 млрд',
                '',
            ],
            'test_2' => [
                '/date/hash/title.id.html',
                "|^(?'date'\d\d\d\d\-\d\d\-\d\d)/(?'hash'[a-fA-F0-9]{40})/(?'title'[a-z0-9_-]+)\.(?'id'[0-9]+)\.html$|",
                '2018-06-29/fcf91c2c99f829e749bb62cb4ab0aabe5371d573/xiaomi-provela-ipo-po-niznej-granice-privlekla-472-mlrd.318.html',
                318,
                'fcf91c2c99f829e749bb62cb4ab0aabe5371d573',
                'Xiaomi провела IPO по нижней границе, привлекла $4,72 млрд',
                '2018-06-29',
            ],
            'test_3' => [
                'hash',
                "|^(?'hash'[a-fA-F0-9]{40})$|",
                'fcf91c2c99f829e749bb62cb4ab0aabe5371d573',
                null,
                'fcf91c2c99f829e749bb62cb4ab0aabe5371d573',
                '',
                '',
            ],
            'test_4' => [
                'id',
                "|^(?'id'[0-9]+)$|",
                '318',
                318,
                '',
                '',
                '',
            ],
            'test_5' => [
                'hash.id',
                "|^(?'hash'[a-fA-F0-9]{40})\.(?'id'[0-9]+)$|",
                'fcf91c2c99f829e749bb62cb4ab0aabe5371d573.318',
                318,
                'fcf91c2c99f829e749bb62cb4ab0aabe5371d573',
                '',
                '',
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
     */
    public function testGenerateFromEntity(string $rule, string $pattern, string $slug, ?int $id, ?string $hash, ?string $title, ?string $date)
    {
        $article = $this->createMock(EntityInterface::class);
        
        $article->method('getId')->willReturn((int)$id);
        
        $article->method('getTitle')->willReturn((string)$title);
        
        $article->method('getHash')->willReturn((string)$hash);
        
        $article->method('getCreatedAt')->willReturn(new \DateTime((string)$date));
        
        $slugger = new Slugger($this->transliterator, $rule);
        
        $actual = $slugger->generateFromEntity($article);
        
        $this->assertSame($slug, $actual);
    }
    
    /**
     * @throws SluggerException
     */
    public function testParseThrowsExceptionOnBadData()
    {
        $slugger = new Slugger($this->transliterator, 'id.hash');
        
        $slug = '318.bad-hash-here';
        
        $this->expectException(SluggerException::class);
        
        $slugger->parse($slug);
        
    }
    
    /**
     *
     */
    protected function setUp()
    {
        $this->transliterator = new IntlTransliterator();
    }
}
