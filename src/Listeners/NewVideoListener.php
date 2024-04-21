<?php


namespace App\Listeners;

use App\Entity\Video;
use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class NewVideoListener
{

    public function __construct(\Twig\Environment $templating, \Swift_Mailer $mailer)
    {

        $this->templating = $templating;
        $this->mailer = $mailer;

    }


    // the listener methods receive an argument which gives you access to
    // both the entity object of the event and the entity manager itself

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        // if this listener only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$entity instanceof Video) {
            return;
        }

        $entityManager = $args->getObjectManager();
        
        $users = $entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user)
        {

            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('email@email.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->templating->render(
                        'emails/new_video.html.twig',
                        [
                            'name' => $user->getName(),
                            'video' => $entity
                        ]
                    ),
                    'text/html'
                );
                
                $this->mailer->send($message);

        }
    }
}