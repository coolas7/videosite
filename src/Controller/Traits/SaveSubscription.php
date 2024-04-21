<?php

namespace App\Controller\Traits;

use App\Entity\Subscription;


trait SaveSubscription {
	

	private function SaveSubscription($plan, $user)
	{

        $date = new \DateTime();
        $date->modify('+1 month');
        $subscription = $user->getSubscription();


        if (null === $subscription)
        {
        	$subscription = new Subscription();
        }


        if ($subscription->isFreePlanUsed() && $plan == Subscription::getPlanDataNameByIndex(0)) // free plan
        {

        	return;

        }


        $subscription->setValidTo($date);
        $subscription->setPlan($plan);


        if ($plan == Subscription::getPlanDataNameByIndex(0))
        {
        	
        	$subscription->setFreePlanUsed(true);
        	$subscription->setPaymentStatus('paid');

        }

        $subscription->setPaymentStatus('paid');



        $user->setSubscription($subscription);

		$entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();


	}


}