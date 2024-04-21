<?php

namespace App\Utils;


use App\Utils\AbstractClasses\CategoryTreeAbstract;
use App\Twig\AppExtension;


class CategoryTreeFrontPage extends CategoryTreeAbstract {


	public function getCategoryList(array $categories_array)
	{

		$this->categoryList .= '<ul>';

		foreach ($categories_array as $category) {

			$catName = $this->slugger->slugify($category->getName());
			$catId = $category->getId();
			$url = $this->urlGenerator->generate('video_list', 
			['categoryname' => $catName, 'id' => $catId]);

			$this->categoryList .= '<li>' . '<a href="' .$url. '">' . $catName . '</a>';

			if ($category->getChildren()) {
				$this->getCategoryList($category->getChildren());
			}

			$this->categoryList .= '</li>';

		}

		$this->categoryList .= '</ul>';

		return $this->categoryList;

	}

	public function getCategoriesData($id)
	{
		$this->slugger = new AppExtension;

		$mainCategory = $this->getMainCategory($id);
		$this->mainCategory = $mainCategory;
		$this->currentCategory = $this->getCurrentCategory($id);
		$subcategories = $this->buildTree($mainCategory->getId());

		return $this->getCategoryList($subcategories);

	}


	public function getChildIds(int $parent): array
	{
		static $ids = [];

		foreach ($this->categoriesArrayFromDb as $category)
		{
			if ($category->getParent() !== null) {
				
				$parent_id = $category->getParent()->getId();
				

				if ($parent_id == $parent)
				{
					$ids[] = $category->getId().',';
					$this->getChildIds($category->getId());
				}	
			}

		}

		return $ids;
	}

}