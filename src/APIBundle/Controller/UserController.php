<?php

namespace APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        return new JsonResponse([]);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, methods={"GET", "HEAD"})
     */
    public function infoAction($id)
    {
        return new JsonResponse([]);
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
     */
    public function deleteAction($id)
    {
        return new JsonResponse([]);
    }

}
