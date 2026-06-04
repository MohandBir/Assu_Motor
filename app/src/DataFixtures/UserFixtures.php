<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $user = (new User())
            ->setFname($faker->firstName())
            ->setLname($faker->lastName())
            ->setEmail('admin@gmail.com')
            ->setRoles(["ROLE_ADMIN"])
            ; 
        $user->setPassword($this->hasher->hashPassword($user, 'Adpassword123$')); 
        
        $manager->persist($user);

        for ($i=0; $i < 10; $i++) { 
            $user = (new User())
            ->setFname($faker->firstName())
            ->setLname($faker->lastName())
            ->setEmail("user$i@gmail.com")
            ->setRoles(["ROLE_USER"])
            ; 
            $user->setPassword($this->hasher->hashPassword($user, 'Password123$')); 
        
        $manager->persist($user);
        $this->addReference('user_'. $i, $user);
        }

        $manager->flush();
    }
}
