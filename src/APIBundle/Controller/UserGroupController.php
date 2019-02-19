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
 * @Route("/users/group")
 */
class UserGroupController extends Controller
{
    /**
     * Return group information with associated users to use in api response.
     *
     * @param  UserGroup  $group
     * @return array
     * @author Mykola Martynov
     **/
    private function group_info_with_users(UserGroup $group)
    {
        $info = APIUtils::group_info($group);
        $info['users'] = array_map(function($user) {
            $user_info = APIUtils::user_info($user);
            unset($user_info['group']);
            return $user_info;
        }, $group->getUsers());

        return $info;
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function listAction()
    {
        $user_groups = $this->getDoctrine()->getRepository('AppBundle:UserGroup')->findAll();
        $groups_info = array_map([$this, 'group_info'], $user_groups);

        return new JsonResponse($groups_info);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("group", class="AppBundle:UserGroup")
     */
    public function infoAction(UserGroup $group)
    {
        return new JsonResponse($this->group_info_with_users($group));
    }

    /**
     * @Route("/create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $group_info = $this->fetchGroupInfo($request);
        if (!$this->isValidGroupInfo($group_info)) {
            return new JsonResponse(['status' => 'failed', 'error' => 'misused data']);
        }

        $group = new UserGroup();
        $group->setName($group_info['name']);
        
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
            return new JsonResponse(['status' => 'failed', 'error' => 'group with given name already exists']);
        } catch (\Exception $ex) {
            return new JsonResponse(['status' => 'failed', 'error' => 'unexpected']);
        }

        return new JsonResponse(['status' => 'success', 'group_id' => $group->getId()], JsonResponse::HTTP_CREATED);
    }

    /**
     * Return group information from the post request.
     *
     * @param  Request  $request
     * @return array
     * @author Mykola Martynov
     **/
    private function fetchGroupInfo(Request $request)
    {
        $rr = $request->request;
        $name = $rr->get('name');

        return compact('name');
    }

    /**
     * Return true if group information has all data to create new group.
     *
     * @param  array  $group_info
     * @return boolean
     * @author Mykola Martynov
     **/
    private function isValidGroupInfo($group_info)
    {
        $required = ['name'];

        foreach ($required as $field_name) {
            if (empty($group_info[$field_name])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @Route("/{id}/delete", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("group", class="AppBundle:UserGroup")
     */
    public function deleteAction(UserGroup $group)
    {
        // !!! stub
        return new JsonResponse();
    }

    /**
     * @Route("/{id}/update", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("group", class="AppBundle:UserGroup")
     */
    public function editAction(UserGroup $group)
    {
        // !!! stub
        return new JsonResponse();
    }

}
