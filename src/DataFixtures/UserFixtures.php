<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{

    public function __construct(UserPasswordHasherInterface $password_hash)
    {
        $this->password_hash = $password_hash;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$name, $last_name, $email, $password, $api_key, $roles])
        {

            $user = new User();

            $user->setName($name);
            $user->setLastName($last_name);
            $user->setEmail($email);
            $user->setPassword($this->password_hash->hashPassword($user, $password));
            $user->setVimeoApiKey($api_key);
            $user->setRoles($roles);

            $manager->persist($user);

        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            ['Vardas', 'Pavarde', 'el@pastas.lt', '12345', '1f953d53e16a044ab0a61b28423625e8', ['ROLE_ADMIN']],
            ['Vardas2', 'Pavarde2', '2el@pastas.lt', '123456', 'hjd8dehdh', ['ROLE_USER']],
            ['Vardas3', 'Pavarde3', '3el@pastas.lt', '1234567', 'hjd8dehdh', ['ROLE_ADMIN']],
            ['Vardas4', 'Pavarde4', '4el@pastas.lt', '55555', 'hjd8dehdh', ['ROLE_USER']],
        ];
    }
}
