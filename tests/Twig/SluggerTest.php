<?php

namespace App\Tests\Twig;

use PHPUnit\Framework\TestCase;
use App\Twig\AppExtension;


class SluggerTest extends TestCase
{
    /**
    * @dataProvider getSlugs
    */
    public function testSlugify(string $string, string $slug): void
    {
        $slugger = new AppExtension;

        $this->assertSame($slug, $slugger->slugify($string));
    }

    public function getSlugs()
    {
        return [
            ['Testas Pirmas', 'testas-pirmas'],
            ['Testas Antras', 'testas-antras'],
        ];
    }
}
