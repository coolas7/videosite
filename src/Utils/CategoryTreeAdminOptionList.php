<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;


class CategoryTreeAdminOptionList extends CategoryTreeAbstract {


	public function getCategoryList(array $categories_array, int $repeat = 0)
	{

		foreach ($categories_array as $category) {

			$this->categoryList[] = ['name' => str_repeat("-", $repeat).$category->getName(),
			'id' => $category->getId()];


			if ($category->getChildren()) {
				$repeat = $repeat + 2;
				$this->getCategoryList($category->getChildren(), $repeat);
				$repeat = $repeat - 2;
			}

		}

		return $this->categoryList;

	}
}