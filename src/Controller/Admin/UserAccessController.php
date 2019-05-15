<?php

namespace App\Controller\Admin;

use App\Entity\UserAccess;
use App\Form\UserAccessForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user/access")
 * @IsGranted("ROLE_ADMIN")
 */
class UserAccessController extends AbstractController
{
    /**
     * @Route("/", name="admin_user_access_index", methods={"GET"})
     */
    public function index(): Response
    {
        $userAccesses = $this->getDoctrine()
            ->getRepository(UserAccess::class)
            ->findAll();

        return $this->render('admin/user_access/index.html.twig', [
            'user_accesses' => $userAccesses,
        ]);
    }

    /**
     * @Route("/new", name="admin_user_access_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $userAccess = new UserAccess();
        $form = $this->createForm(UserAccessForm::class, $userAccess);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userAccess);
            $entityManager->flush();

            return $this->redirectToRoute('user_access_index');
        }

        return $this->render('admin/user_access/new.html.twig', [
            'user_access' => $userAccess,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_access_show", methods={"GET"})
     */
    public function show(UserAccess $userAccess): Response
    {
        return $this->render('admin/user_access/show.html.twig', [
            'user_access' => $userAccess,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_access_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserAccess $userAccess): Response
    {
        $form = $this->createForm(UserAccessForm::class, $userAccess);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_user_access_index', [
                'id' => $userAccess->getId(),
            ]);
        }

        return $this->render('admin/user_access/edit.html.twig', [
            'user_access' => $userAccess,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_access_delete", methods={"DELETE"})
     */
    public function delete(Request $request, UserAccess $userAccess): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userAccess->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userAccess);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_access_index');
    }
}
