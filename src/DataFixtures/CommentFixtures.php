<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach($this->CommentData() as [$content, $user, $video, $created_at])
        {

            $comment = new Comment;

            $user = $manager->getRepository(User::class)->find($user);
            $video = $manager->getRepository(Video::class)->find($video);

            $comment->setContent($content);
            $comment->setUser($user);
            $comment->setVideo($video);
            $comment->setCreatedAtForFixtures(new \DateTime($created_at));

            $manager->persist($comment);

        }

        $manager->flush();
    }

    private function CommentData()
    {

        return [
            ['1 This is user comment about something1',1,10,'2023-01-02 12:11:56'],
            ['2 This is user comment about something1',1,10,'2023-01-03 13:11:56'],
            ['3 This is user comment about something1',2,11,'2023-01-12 15:11:56'],
            ['4 This is user comment about something1',3,11,'2023-01-22 17:11:56'],
            ['16 This is user comment about something1',1,11,'2023-04-12 12:55:56'],
            ['17 This is user comment about something1',1,9,'2023-05-03 14:14:56'],
            ['18 This is user comment about something1',2,9,'2023-01-05 18:11:56'],
            ['10 This is user comment about something1',1,10,'2023-02-02 12:22:56'],
        ];
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class
        );
    }
}
