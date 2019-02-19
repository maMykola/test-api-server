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
 * @Route("/users/group")
 */
class UserGroupController extends Controller
{
    /**
     * @Route("/", methods={"GET"})
     */
    public function listAction()
    {
        // !!! stub
        return new JsonResponse();
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("group", class="AppBundle:UserGroup")
     */
    public function infoAction(UserGroup $group)
    {
        // !!! stub
        return new JsonResponse();
    }

    /**
     * @Route("/create", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        // !!! stub
        return new JsonResponse();
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
