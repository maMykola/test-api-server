<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * Return user information to use in api responses.
     *
     * @param  User  $user
     * @return array
     * @author Mykola Martynov
     **/
    private function user_info(User $user)
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
     * @Route("/", methods={"GET"})
     */
    public function listAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $users_info = array_map([$this, 'user_info'], $users);

        return new JsonResponse($users_info);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET", "HEAD"})
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function infoAction(User $user)
    {
        return new JsonResponse($this->user_info($user));
    }

    /**
     * @Route("/create", methods={"PUT"})
     */
    public function createAction()
    {
        return new JsonResponse([]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function deleteAction(User $user)
    {
        return new JsonResponse([]);
    }

}
