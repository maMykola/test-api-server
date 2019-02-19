<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
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
    public function createAction(Request $request)
    {
        $user_info = $this->fetchUserInfo($request);
        if (!$this->isValidUserInfo($user_info)) {
            return new JsonResponse(['status' => 'failed']);
        }

        $user = new User();
        $user
            ->setName($user_info['name'])
            ->setEmail($user_info['email'])
        ;

        $group = $this->getUserGroup($user_info['group']);
        if (!empty($group)) {
            $user->setGroup($group);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse(['status' => 'success', 'user_id' => $user->getId()]);
    }

    /**
     * Return user info from the post request.
     *
     * @param  Request  $request
     * @return array
     * @author Mykola Martynov
     **/
    private function fetchUserInfo(Request $request)
    {
        // !!! stub
        return [];
    }

    /**
     * Return true if user information has all data to create new user.
     *
     * @param  array  $user_info
     * @return boolean
     * @author Mykola Martynov
     **/
    private function isValidUserInfo($user_info)
    {
        // !!! stub
        return false;
    }

    /**
     * Return group entity with the given name.
     *
     * @param  string  $group_name
     * @return UserGroup
     * @author Mykola Martynov
     **/
    private function getUserGroup($group_name)
    {
        // !!! stub
        return null;
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
