<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Video;
use App\Entity\User;

class VideoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        
        foreach($this->VideoData() as [$title, $path, $category_id])
        {

            $duration = random_int(10,300);
            $category = $manager->getRepository(Category::class)->findOneById($category_id);

            $video = new Video();
            $video->setTitle($title);
            $video->setPath('https://player.vimeo.com/video/'.$path);
            $video->setCategory($category);
            $video->setDuration($duration);
            $manager->persist($video);


        }

        $manager->flush();

        $this->loadLikes($manager);
        $this->loadDislikes($manager);

    }

    public function loadLikes($manager)
    {

        foreach($this->likesData() as [$video_id, $user_id] )
        {

            $video = $manager->getRepository(Video::class)->find($video_id);
            $user = $manager->getRepository(User::class)->find($user_id);

            $video->addUsersThatLike($user);
            $manager->persist($video);
        }

        $manager->flush();


    }

        public function loadDislikes($manager)
    {

        foreach($this->dislikesData() as [$video_id, $user_id] )
        {

            $video = $manager->getRepository(Video::class)->find($video_id);
            $user = $manager->getRepository(User::class)->find($user_id);

            $video->addUsersThatDontlike($user);
            $manager->persist($video);
        }

        $manager->flush();


    }

    private function VideoData()
    {

        return [
            ['Movies 1', 5722542,4],
            ['Movies 2', 4654954,4],
            ['Movies 3', 5425255,4],
            ['Movies 4', 5555622,4],
            ['Movies 5', 5722542,16],
            ['Movies 6', 4654954,16],
            ['Movies 7', 5425255,17],
            ['Movies 8', 5555622,19],
            ['Movies 9', 5555622,19],
            ['Movies 10', 5555622,20],
            ['Electronic 1', 2125253,1],
            ['Electronic 6', 1453256,1],
            ['Electronic 7', 6854222,1],
            ['Electronic 8', 4458663,1],
            ['Book 1', 5722542,3],
            ['Book 2', 4654954,3],
            ['Book 3', 5425255,3],
            ['Book 4', 5555622,3],
        ];

    }


    private function likesData()
    {

        return [
            [12,1],
            [11,1],
            [1,1],
            [2,1],
            [1,2],
            [2,2],
            [12,2],
            [11,2],
            [10,3],
            [9,3],
            [1,3],
            [3,3],
        ];

    }


        private function dislikesData()
    {

        return [
            [12,1],
            [10,1],
            [1,1],
            [2,1],
            [1,2],
            [8,2],
            [12,2],
            [11,2],
            [10,3],
            [8,3],
            [1,3],
            [4,3],
        ];

    }


}
