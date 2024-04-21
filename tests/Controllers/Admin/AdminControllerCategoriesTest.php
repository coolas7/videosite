<?php

namespace App\Tests\Controllers\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Category;

class AdminControllerCategoriesTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->client->disableReboot();

        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);

    }

    public function tearDown(): void
    {
        parent::tearDown();

        if($this->entityManager->getConnection()->isTransactionActive())
        {
            $this->entityManager->rollback();
            $this->entityManager->close();
            $this->entityManager = null;   
        } 
     

    }

    public function testTextOnPage(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');

        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertResponseIsSuccessful('Electronics', $this->client->getResponse()->getContent());

    }

    public function testNumberOfItems(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');

        $this->assertCount(24, $crawler->filter('option'));

    }


        public function testNewCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/categories');
        $form = $crawler->selectButton('Add')->form([
            'category[parent]' => 1,
            'category[name]' => 'Other electronics',
        ]);

        $this->client->submit($form);

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findOneBy(['name'=>'Other electronics']);

        $this->assertNotNull($category);
        $this->assertSame('Other electronics', $category->getName());

    }

        public function testEditCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/edit-category/6');
        $form = $crawler->selectButton('Save')->form([
            'category[parent]' => 1,
            'category[name]' => 'Computers2',
        ]);

        $this->client->submit($form);

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findOneById(6);

        $this->assertNotNull($category);
        $this->assertSame('Computers2', $category->getName());

    }


        public function testDeleteCategory(): void
    {
        $crawler = $this->client->request('GET', '/admin/delete-category/6');

        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findOneById(6);

        $this->assertNull($category);

    }

}
