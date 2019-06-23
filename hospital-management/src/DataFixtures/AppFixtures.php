<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {

        $adminUser = new User();
        $adminUser->setEmail("bobafett@email.com");
        $adminUser->setFirstName("Boba");
        $adminUser->setLastName("Fett");
        $adminUser->setRoles(["ROLE_ADMIN"]);
        $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser,"fettacheese"));
        $manager->persist($adminUser);

        $endUser = new User();
        $endUser->setEmail("lukeskywalker@email.com");
        $endUser->setFirstName("Luke");
        $endUser->setLastName("Skywalker");
        $endUser->setRoles(["ROLE_USER"]);
        $endUser->setPassword($this->passwordEncoder->encodePassword($endUser,"skieswalker"));
        $manager->persist($endUser);

        $manager->flush();
    }
}
