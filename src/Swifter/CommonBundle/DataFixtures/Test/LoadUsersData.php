<?php

namespace Swifter\CommonBundle\DataFixtures\Test;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Swifter\UserBundle\Entity\Role;
use Swifter\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUsersData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        /* Roles. */
        $role1 = new Role();
        $role1->setRole('ROLE_USER');
        $role1->setDescription('Login privileges, granted after registration.');

        $role2 = new Role();
        $role2->setRole('ROLE_ADMIN');
        $role2->setDescription('Administrative user.');

        $manager->persist($role1);
        $manager->persist($role2);
        $manager->flush();

        /* Users */
        $user1 = new User();
        $user1->setEmail('admin@m.com');
        $user1->setName('Admin1');
        $user1->setEnabled(1);
        $user1->setPassword('c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec');
        $user1->setRoles(array($role2));

        $user2 = new User();
        $user2->setEmail('user@m.com');
        $user2->setName('User1');
        $user2->setEnabled(1);
        $user2->setPassword('c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec');
        $user2->setRoles(array($role1));

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();
    }
}