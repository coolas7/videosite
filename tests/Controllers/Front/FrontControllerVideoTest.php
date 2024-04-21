<?php

namespace App\Tests\Controllers\Front;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerVideoTest extends WebTestCase
{
    public function testNoResults(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/');

        $form = $crawler->selectButton('Search video')->form([
            'query' => 'dsdd',
        ]);

        $crawler = $client->submit($form);
        $this->assertSame('No results were found', $crawler->filter('h1')->text());

    }

    // public function testResultsFound(): void
    // {
    //     $client = static::createClient();
    //     $client->followRedirects();

    //     $crawler = $client->request('GET', '/');

    //     $form = $crawler->selectButton('Search video')->form([
    //         'query' => 'Movies',
    //     ]);
    //     $crawler = $client->submit($form);
    //     $this->assertGreaterThan(0, $crawler->filter('h3')->count());
    // }
}
