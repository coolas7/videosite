<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Category;
use App\Entity\Video;
use App\Utils\CategoryTreeFrontPage;
use App\Repository\VideoRepository;
use App\Entity\Comment;
use App\Controller\Traits\Likes;
use App\Utils\VideoForNoValidSubscription;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Utils\Interfaces\CacheInterface;

class FrontController extends AbstractController
{
    use Likes;
    /**
     * @Route("/", name="main_page")
     */
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }


    /**
     * @Route("/video-list/category/{categoryname},{id}/{page}", name="video_list", defaults={"page": "1"})
     */
    public function videoList(
        $id,
        $page, 
        CategoryTreeFrontPage $categories,
        ManagerRegistry $doctrine,
        Request $request,
        VideoForNoValidSubscription $video_no_members,
        CacheInterface $cache
        )
    {

        $cache = $cache->cache;

        $video_list = $cache->getItem('video_list'.$id.$page.$request->get('sortby'));
        $video_list->expiresAfter(60);

        if (!$video_list->isHit())
        {
            
            $ids = $categories->getChildIds($id);
            array_push($ids, $id);

            $videos = $doctrine->getRepository(Video::class)
            ->findByChildIds($ids, $page, $request->get('sortby'));

            $categories->getCategoriesData($id);

            $response = $this->render('front/video_list.html.twig', [
                'categories' => $categories,
                'videos' => $videos,
                'video_no_members' => $video_no_members->check(),
            ]);

            $video_list->set($response);
            $cache->save($video_list);

        }

        return $video_list->get();




        // $categories->getCategoriesData($id);

        // $ids = $categories->getChildIds($id);
        // array_push($ids, $id);

        // $videos = $doctrine->getRepository(Video::class)
        // ->findByChildIds($ids, $page, $request->get('sortby'));

        // return $this->render('front/video_list.html.twig', [
        //     'categories' => $categories,
        //     'videos' => $videos,
        //     'video_no_members' => $video_no_members->check(),
        // ]);
    }


    /**
     * @Route("/video-details/{video}", name="video_details")
     */
    public function videoDetails(VideoRepository $repo, $video, VideoForNoValidSubscription $video_no_members): Response
    {
        return $this->render('front/video_details.html.twig', [
            'video_no_members' => $video_no_members->check(),
            'video'=>$repo->videoDetails($video),
        ]);
    }


    /**
     * @Route("/search-results/{page}", name="search_results", methods={"GET"}, defaults={"page": "1"})
     */
    public function searchResults($page, Request $request, ManagerRegistry $doctrine, VideoForNoValidSubscription $video_no_members): Response
    {
        $videos = null;
        $query = null;

        if ($query = $request->get('query'))
        {
            $videos = $doctrine->getRepository(Video::class)
            ->findByTitle($query, $page, $request->get('sortby'));

            if (!$videos->getItems()) $videos = null;
        }


        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'video_no_members' => $video_no_members->check(),
            'query' => $query,
        ]);
    }


    public function mainCategories(ManagerRegistry $doctrine)
    {

        $categories = $doctrine->getRepository(Category::class)
        ->findBy(['parent' => null], ['name' => 'ASC']);

        return $this->render('front/main_categories.html.twig', [
            'categories' => $categories,
        ]);

    }


    /**
     * @Route("/new-comment/{video}", methods={"POST"}, name="new_comment")
     */

    public function newComment(Video $video, Request $request) 
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        if ( !empty( trim($request->request->get('comment')) )) {
            
            $comment = new Comment();
            $comment->setContent($request->request->get('comment'));
            $comment->setUser($this->getUser());
            $comment->setVideo($video);

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }


        return $this->redirectToRoute('video_details', ['video'=>$video->getId()]);
    }



    /**
     * @Route("/delete-comment/{comment}", name="delete_comment")
     * @Security("user.getId() == comment.getUser().getId()")
     */

    public function deleteComment(Comment $comment, Request $request) 
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        

        return $this->redirect($request->headers->get('referer'));
    }


    /**
     * @Route("/video-list/{video}/like", methods={"POST"}, name="like_video")
     * @Route("/video-list/{video}/dislike", methods={"POST"}, name="dislike_video")
     * @Route("/video-list/{video}/unlike", methods={"POST"}, name="undo_like_video")
     * @Route("/video-list/{video}/undodislike", methods={"POST"}, name="undo_dislike_video")
     */

    public function toggleLikesAjax(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        switch($request->get('_route'))
        {

            case 'like_video':
                $result = $this->likeVideo($video);
                break;

            case 'dislike_video':
                $result = $this->dislikeVideo($video);
                break;

            case 'undo_like_video':
                $result = $this->undoLikeVideo($video);
                break;

            case 'undo_dislike_video':
                $result = $this->undoDislikeVideo($video);
                break;
                
        }

        return $this->json(['action' => $result, 'id' => $video->getId()]);

    }
      
}
