<?php

namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
// use Doctrine\Persistence\ManagerRegistry;
// use App\Utils\CategoryTreeAdminPage;
use App\Entity\Video;
use App\Entity\User;
use App\Form\UserType;

/**
 * @Route("/admin")
 */

class MainController extends AbstractController
{


    /**
     * @Route("/", name="admin_main_page")
     */
    public function index(Request $request, UserPasswordEncoderInterface $password_encoder, TranslatorInterface $translator): Response
    {

        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['user' => $user]);
        $form->handleRequest($request);
        $is_invalid = null;

        if ($form->isSubmitted() && $form->isValid()) {


            $em = $this->getDoctrine()->getManager();

            $user->setName($request->request->get('user')['name']);
            $user->setLastname($request->request->get('user')['last_name']);
            $user->setEmail($request->request->get('user')['email']);

            $password = $password_encoder->encodePassword($user, $request->request->get('user')['password']['first']);
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            $translated = $translator->trans('Changes successfully saved!');

            $this->addFlash(
                'success',
                $translated
            );

            return $this->redirectToRoute('admin_main_page');

        }
        elseif ( $request->isMethod('post') )
        {

            $is_invalid = 'is-invalid';
            
        }



        return $this->render('admin/my_profile.html.twig', [
        	'subscription' => $this->getUser()->getSubscription(),
            'form' => $form->createView(),
            'is_invalid' => $is_invalid,
        ]);
    }


    /**
     * @Route("/videos", name="videos")
     */
    public function videos(CategoryTreeAdminOptionList $categories): Response
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {

            $categories->getCategoryList($categories->buildTree());
            $videos = $this->getDoctrine()->getRepository(Video::class)->findBy([],['title' => 'ASC']);

        }
        else
        {
            $categories = null;
            $videos = $this->getUser()->getLikedVideos();

        }

        return $this->render('admin/videos.html.twig', [
                'videos' => $videos,
                'categories' => $categories,
        ]);
    }


    /**
     * @Route("/cancel-plan", name="cancel_plan")
     */
    public function cancelPlan(): Response
    {

    	$user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());

    	$subscription = $user->getSubscription();
    	$subscription->setValidTo(new \DateTime());
    	$subscription->setPaymentStatus(null);
    	$subscription->setPlan('canceled');

    	$em = $this->getDoctrine()->getManager();
    	$em->persist($user);
    	$em->persist($subscription);
    	$em->flush();

    	return $this->redirectToRoute('admin_main_page');

    }


    /**
     * @Route("/delete-account", name="delete_account")
     */
    public function deleteAccount(): Response
    {
        // $user = $this->getUser();
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        session_destroy();


        return $this->redirectToRoute('main_page');

    }

}