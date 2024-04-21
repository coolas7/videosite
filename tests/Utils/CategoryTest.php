<?php

namespace App\Tests\Utils;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Twig\AppExtension;
use PHPUnit\Framework\MockObject\MockBuilder;
use App\Entity\Category;
use App\Utils\CategoryTreeFrontPage;

class CategoryTest extends KernelTestCase
{

    protected $mockedCategoryTreeFrontPage;
    protected $mockedCategoryTreeAdminList;
    protected $mockedCategoryTreeAdminOptionList;


    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $urlGenerator = $kernel->getContainer()->get('router');
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $tested_classes = [
            'CategoryTreeFrontPage',
            'CategoryTreeAdminOptionList',
            'CategoryTreeAdminPage',
        ];

        foreach($tested_classes as $class)
        {
            $name = 'mocked'.$class;
            $this->$name = $this->getMockBuilder('\App\Utils\\'.$class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();

            $this->$name->urlGenerator = $urlGenerator;

        }
    }


    /**
    * @dataProvider dataForCategoryTreeFrontPage
    */
    public function testCategoryTreeFrontPage($string, $id)
    {

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findOneById($id);

        $categoryList = $this->entityManager
            ->getRepository(Category::class)
            ->findAll();

        $this->mockedCategoryTreeFrontPage->slugger = new AppExtension;
        $this->mockedCategoryTreeFrontPage->categoriesArrayFromDb = $categoryList;

        $this->assertSame(null, $category->getParent());

        $array = $this->mockedCategoryTreeFrontPage->buildTree($id);
        $this->assertSame($string, $this->mockedCategoryTreeFrontPage->getCategoryList($array));
    }


    public function dataForCategoryTreeFrontPage()
    {

    yield [
        '<ul><li><a href="/video-list/category/family,16">family</a></li><li><a href="/video-list/category/romance,17">romance</a><ul><li><a href="/video-list/category/romantic-comedy,19">romantic-comedy</a></li><li><a href="/video-list/category/romantic-drama,23">romantic-drama</a></li></ul></li><li><a href="/video-list/category/thriller,18">thriller</a></li></ul>',
        4
    ];


    }
}
