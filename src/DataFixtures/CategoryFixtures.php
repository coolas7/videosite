<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;

class CategoryFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {

        $this->loadMainCategories($manager);
        $this->loadElectronics($manager);
        $this->loadComputers($manager);
        $this->loadLaptops($manager);
        $this->loadBooks($manager);
        $this->loadMovies($manager);
        $this->loadRomance($manager);

    }


    private function getMainCategoriesData()
    {

        return [
            ['Electronics', 1], 
            ['Toys', 2], 
            ['Books', 3],
            ['Movies', 4],
        ];

    }


    private function loadMainCategories($manager)
    {

        foreach ( $this->getMainCategoriesData() as [$name] )
        {

            $category = new Category();
            $category->setName($name);
            $manager->persist($category);

        }


        $manager->flush();

    }


    private function loadSubCategories($manager, $category, $parent_id)
    {
        $parent = $manager->getRepository(Category::class)->find($parent_id);

        $methodName = "get{$category}Data";

        foreach ( $this->$methodName() as [$name] )
        {

            $category = new Category();
            $category->setName($name);
            $category->setParent($parent);
            $manager->persist($category);

        }


        $manager->flush();

    }


    private function getElectronicsData()
    {

        return [
            ['Cameras', 5], 
            ['Computers', 6], 
            ['Cell Phones', 7],
        ];

    }


    private function loadElectronics($manager) 
    {


        $this->loadSubCategories($manager, 'Electronics', 1);


    }


    private function getComputersData()
    {

        return [
            ['Laptops', 8], 
            ['Dekstops', 9], 
        ];

    }


    private function loadComputers($manager) 
    {


        $this->loadSubCategories($manager, 'Computers', 6);


    }


    private function getLaptopsData()
    {

        return [
            ['Apple', 10], 
            ['HP', 11],
            ['Dell', 12], 
            ['Lenovo', 13], 
        ];

    }


    private function loadLaptops($manager) 
    {


        $this->loadSubCategories($manager, 'Laptops', 8);


    }



    private function getBooksData()
    {

        return [
            ['For childrens', 14], 
            ['Fantasy', 15], 
        ];

    }


    private function loadBooks($manager) 
    {


        $this->loadSubCategories($manager, 'Books', 3);


    }


    private function getMoviesData()
    {

        return [
            ['Family', 16], 
            ['Romance', 17],
            ['Thriller', 18],

        ];

    }


    private function loadMovies($manager) 
    {


        $this->loadSubCategories($manager, 'Movies', 4);


    }



    private function getRomanceData()
    {

        return [
            ['Romantic Comedy', 19], 
            ['Romantic Drama', 20],
        ];

    }


    private function loadRomance($manager) 
    {


        $this->loadSubCategories($manager, 'Romance', 17);


    }

}
