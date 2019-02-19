<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DataFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $groups_data = ['group1', 'group2', 'group3'];
        $users_data = [
            ['John Doe', 'john.doe@example.com'],
            ['John Vue', 'john.vue@example.com'],
            ['Mikel Fox', 'mikel.fox@example.com'],
            ['Ben Laden', 'ben.laden@example.com'],
            ['Sam Deniels', 'sam.deniels@example.com'],
        ];

        # create groups
        $groups = [];
        foreach ($groups_data as $group_name) {
            $group = new UserGroup();
            $group->setName($group_name);
            $manager->persist($group);

            $groups[] = $group;
        }

        # create users
        $total_groups = count($groups);
        foreach ($users_data as $pos => $user_info) {
            $user = new User();
            $user
                ->setName($user_info[0])
                ->setEmail($user_info[1])
                ->setGroup($groups[$pos % $total_groups])
            ;
            $manager->persist($user);
        }

        $manager->flush();
    }
}
