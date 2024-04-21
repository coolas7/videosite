<?php

namespace App\Controller\Admin\Superadmin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Utils\CategoryTreeAdminPage;
use App\Utils\CategoryTreeAdminOptionList;
use App\Entity\Category;
use App\Form\CategoryType;


/**
 * @Route("/admin/su")
 */

class CategoriesController extends AbstractController
{


    /**
     * @Route("/categories", name="categories", methods={"GET", "POST"})
     */
    public function categories(CategoryTreeAdminPage $categories, Request $request, ManagerRegistry $doctrine)
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;

        if ( $this->saveCategory($form, $category, $request, $doctrine) ) 
        {
            return $this->redirectToRoute('categories');
        }
        elseif ($request->isMethod('post')) 
        {
            $is_invalid = ' is-invalid';
        }


        return $this->render('admin/categories.html.twig', [
            'categories' => $categories->categoryList,
            'form' => $form->createView(),
            'is_invalid' => $is_invalid,
        ]);
    }


    private function saveCategory($form, $category, $request, $doctrine)
    {
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $category->setName($request->request->get('category')['name']);
            
            $parentCategory = $doctrine->getRepository(Category::class)
            ->find($request->request->get('category')['parent']);

            $category->setParent($parentCategory);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return true;
        }

        return false;
    }


    /**
     * @Route("/delete-category/{id}", name="delete_category")
     */
    public function deleteCategory(Category $category, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('categories');
    }


    /**
     * @Route("/edit-category/{id}", name="edit_category", methods={"GET", "POST"})
     */
    public function editCategory(Category $category, Request $request, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;

        if ( $this->saveCategory($form, $category, $request, $doctrine) ) 
        {
            return $this->redirectToRoute('categories');
        }
        elseif ($request->isMethod('post')) 
        {
            $is_invalid = ' is-invalid';
        }

        return $this->render('admin/edit_category.html.twig',[
            'category' => $category,
            'form' => $form->createView(),
            'is_invalid' => $is_invalid,
        ]);
    }


    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $categories->getCategoryList($categories->buildTree());

        return $this->render('admin/all_categories.html.twig',[
            'categories' => $categories,
            'editedCategory' => $editedCategory,
        ]);
    }

}