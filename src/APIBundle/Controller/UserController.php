<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Entity\UserGroup;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use APIBundle\Utils\APIUtils;

/**
 * @Route("/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function listAction()
    {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $users_info = array_map(['APIBundle\Utils\APIUtils', 'user_info'], $users);

        return new JsonResponse($users_info);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET", "HEAD"})
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function infoAction(User $user)
    {
        return new JsonResponse(APIUtils::user_info($user));
    }

    /**
     * @Route("/create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $user_info = $this->fetchUserInfo($request);
        if (!$this->isValidUserInfo($user_info)) {
            return new JsonResponse(['status' => 'failed', 'error' => 'misused data']);
        }

        $this->modifyUserInfo($user_info);

        $user = new User();
        $user
            ->setName($user_info['name'])
            ->setEmail($user_info['email'])
            ->setGroup($user_info['group'])
        ;

        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return new JsonResponse(['status' => 'failed', 'error' => 'user with given email already exists']);
        } catch (\Exception $ex) {
            return new JsonResponse(['status' => 'failed', 'error' => 'unexpected']);
        }

        return new JsonResponse(['status' => 'success', 'user_id' => $user->getId()], JsonResponse::HTTP_CREATED);
    }

    /**
     * Change some fields to have valid types.
     *
     * @param  array  $user_info
     * @return void
     * @author Mykola Martynov
     **/
    private function modifyUserInfo(&$user_info)
    {
        if (array_key_exists('group', $user_info)) {
            $user_info['group'] = $this->getUserGroup($user_info['group']);
        }
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
        $rr = $request->request;
        $name = $rr->get('name');
        $email = $rr->get('email');
        $group = $rr->get('group');

        return compact('name', 'email', 'group');
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
        $required = ['name', 'email'];

        foreach ($required as $field_name) {
            if (empty($user_info[$field_name])) {
                return false;
            }
        }

        return true;
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
        if (empty($group_name)) {
            return null;
        }

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:UserGroup');

        $group = $repo->findOneByName($group_name);
        if (empty($group)) {
            $group = new UserGroup();
            $group->setName($group_name);
            $em->persist($group);
        }

        return $group;
    }

    /**
     * @Route("/{id}/delete", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("user", class="AppBundle:User")
     */
    public function deleteAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return new JsonResponse(['status' => 'success']);
    }

    /**
     * @Route("/{id}/update", requirements={"id"="\d+"}, methods={"POST"})
     * @ParamConverter("user", class="AppBundle:User")
     **/
    public function editAction(User $user, Request $request)
    {
        $user_info = array_filter($this->fetchUserInfo($request));
        if (empty($user_info)) {
            return new JsonResponse(['status' => 'failed', 'error' => 'misused data']);
        }

        $this->modifyUserInfo($user_info);
        $fields_list = ['name', 'email', 'group'];

        foreach ($fields_list as $field_name) {
            if (!array_key_exists($field_name, $user_info)) {
                continue;
            }

            $method = 'set' . ucfirst($field_name);
            $user->$method($user_info[$field_name]);
        }

        try {
            $this->getDoctrine()->getManager()->flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return new JsonResponse(['status' => 'failed', 'error' => 'new email address already used']);
        } catch (\Exception $ex) {
            return new JsonResponse(['status' => 'failed', 'error' => 'unexpected']);
        }

        return new JsonResponse(['status' => 'success']);
    }

}
