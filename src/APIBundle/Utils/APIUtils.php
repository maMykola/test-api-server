<?php

namespace APIBundle\Utils;

use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;

class APIUtils {

    /**
     * Return user information to use in api responses.
     *
     * @param  User  $user
     * @return array
     * @author Mykola Martynov
     **/
    static public function user_info(User $user)
    {
        $info = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ];

        if ($user->hasGroup()) {
            $info['group'] = $user->getGroup()->getName();
        }

        return $info;
    }

    /**
     * Return group information to use in api response.
     *
     * @param  UserGroup  $group
     * @return array
     * @author Mykola Martynov
     **/
    static public function group_info(UserGroup $group)
    {
        $info = [
            'id' => $group->getId(),
            'name' => $group->getName(),
        ];

        return $info;
    }
}
