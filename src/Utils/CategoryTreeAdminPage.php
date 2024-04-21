<?php

namespace App\Utils;

use App\Utils\AbstractClasses\CategoryTreeAbstract;


class CategoryTreeAdminPage extends CategoryTreeAbstract {


	public function getCategoryList(array $categories_array)
	{


		$this->categoryList .= '<ul class="fa-ul text-left">';

		foreach ($categories_array as $category) {

			$catName = $category->getName();
			$catId = $category->getId();
			$url = $this->urlGenerator->generate('video_list', 
			['categoryname' => $catName, 'id' => $catId]);

			$url_edit = $this->urlGenerator->generate('edit_category', ['id' => $catId]);	

			$url_delete = $this->urlGenerator->generate('delete_category', ['id' => $catId]);

			$this->categoryList .= '<li><i class="fa-li fa fa-arrow-right"></i>' . 
			'<a class="mr-3 font-weight-bold" href="' .$url. '">' . $catName . '</a>';

			$this->categoryList .= '<a class="text-primary" href="' .$url_edit. '">Edit</a>';
			$this->categoryList .= '<a class="text-danger" onclick="return confirm(\'Are you sure?\')" href="' .$url_delete. '">Delete</a>';


			if ($category->getChildren()) {
				$this->getCategoryList($category->getChildren());
			}

			$this->categoryList .= '</li>';

		}

		$this->categoryList .= '</ul>';

		return $this->categoryList;



	}






}
