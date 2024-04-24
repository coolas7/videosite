<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 * @ORM\Table(name="subscriptions")
 */
class Subscription
{
    public const ProPlan = 'https://www.sandbox.paypal.com/webapps/billing/plans/subscribe?plan_id=P-6DM89483XA7345003MYT4QYA';
    public const EnterprisePlan = 'https://www.sandbox.paypal.com/webapps/billing/plans/subscribe?plan_id=P-5R0828564A997883RMYT4SBY';

    private static $planDataNames = ['free', 'pro', 'enterprise'];
    private static $planDataPrices = [
        'free' => 0,
        'pro' => 15,
        'enterprise' => 29,
    ];

    public static function getPlanDataNameByIndex(int $index): string
    {
        return self::$planDataNames[$index];
    }

    public static function getPlanDataPriceByName(string $name): int
    {
        return self::$planDataPrices[$name];
    }

    public static function getPlanDataNames(): array
    {
        return self::$planDataNames;
    }

    public static function getPlanDataPrices(): array
    {
        return self::$planDataPrices;
    }
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $plan;

    /**
     * @ORM\Column(type="datetime")
     */
    private $valid_to;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $payment_status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $free_plan_used;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?string
    {
        return $this->plan;
    }

    public function setPlan(string $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->valid_to;
    }

    public function setValidTo(\DateTimeInterface $valid_to): self
    {
        $this->valid_to = $valid_to;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(?string $payment_status): self
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    public function isFreePlanUsed(): ?bool
    {
        return $this->free_plan_used;
    }

    public function setFreePlanUsed(bool $free_plan_used): self
    {
        $this->free_plan_used = $free_plan_used;

        return $this;
    }
}
