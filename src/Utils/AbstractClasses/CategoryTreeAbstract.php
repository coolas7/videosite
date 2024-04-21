<?php 

namespace App\Utils\AbstractClasses;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Category;

abstract class CategoryTreeAbstract {

	public $categoriesArrayFromDb;
	public $categoryList;

	public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
	{

		$this->entityManager = $entityManager;
		$this->urlGenerator = $urlGenerator;
		$this->categoriesArrayFromDb = $this->getCategories();

	}

	abstract public function getCategoryList(array $categories_array);

	public function buildTree(int $parent_id = null)
	{
		
		$subcategory = [];

		foreach($this->categoriesArrayFromDb as $category)
		{
		
			$parentId = null;
			$parent = $category->getParent();

			if ($parent) {
				$parentId = $parent->getId();
			}

			if ($parentId == $parent_id) {
					
				$children = $this->buildTree($category->getId());

				if ($children) {
					$category->setChildren($children); 
				}
				$subcategory[] = $category; 
			}
			
		}

		return $subcategory;

	}


	private function getCategories(): array
	{

		$categories = $this->entityManager
		->getRepository(Category::class)
		->findAll();

		return $categories;
		
	}


	public function getMainCategory($id): object
	{

		$category = $this->entityManager
		->getRepository(Category::class)
		->findOneById($id);

		while ($category !== null) {

			$mainCategory = $category;
			$category = $category->getParent();
		}

		return $mainCategory;

	}


	public function getCurrentCategory($id): object
	{

		$currentCategory = $this->entityManager
		->getRepository(Category::class)
		->findOneById($id);

		return $currentCategory;

	}
}