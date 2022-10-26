<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    // pas besoin de déclarer private UserPasswordHasherInterface $hasher au-dessus dans la classe
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setPassword($this->hasher->hashPassword($admin,'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setFirstName('Admin');
        $admin->setLastName('Admin');
        $manager->persist($admin);

        $john = new User();
        $john->setEmail('john@gmail.com');
        $john->setPassword($this->hasher->hashPassword($john,'john'));
        $john->setFirstName('John');
        $john->setLastName('Doe');
        $manager->persist($john);

        $manager->flush();
    }
}
