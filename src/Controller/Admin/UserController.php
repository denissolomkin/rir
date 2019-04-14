<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage users.
 *
 * @Route("/admin/users")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="admin_user_index")
     */
    public function index(UserRepository $userRepository): Response
    {
        $list = $userRepository->findAll();

        return $this->render('admin/user/list.html.twig', [
            'list' => $list,
        ]);

    }

    /**
     * @Route("/new", methods={"GET"}, name="admin_user_new")
     */
    public function new(): Response
    {

        return $this->render('admin/user/new.html.twig', [
        ]);

    }
}
