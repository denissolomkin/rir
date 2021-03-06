<?php

namespace App\Controller\Moderator;

use App\Entity\MetaPurpose;
use App\Form\MetaPurposeForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderator/meta/purpose")
 * @IsGranted("ROLE_MODERATOR")
 */
class MetaPurposeController extends AbstractController
{
    /**
     * @Route("/", name="moderator_meta_purpose_index", methods={"GET"})
     */
    public function index(): Response
    {
        $resourcePurposes = $this->getDoctrine()
            ->getRepository(MetaPurpose::class)
            ->findAll();

        return $this->render('moderator/meta/purpose/index.html.twig', [
            'resource_purposes' => $resourcePurposes,
        ]);
    }

    /**
     * @Route("/new", name="moderator_meta_purpose_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $resourcePurpose = new MetaPurpose();
        $form = $this->createForm(MetaPurposeForm::class, $resourcePurpose);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resourcePurpose);
            $entityManager->flush();

            return $this->redirectToRoute('moderator_meta_purpose_index');
        }

        return $this->render('moderator/meta/purpose/new.html.twig', [
            'resource_purpose' => $resourcePurpose,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_meta_purpose_show", methods={"GET"})
     */
    public function show(MetaPurpose $resourcePurpose): Response
    {
        return $this->render('moderator/meta/purpose/show.html.twig', [
            'resource_purpose' => $resourcePurpose,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="moderator_meta_purpose_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MetaPurpose $resourcePurpose): Response
    {
        $form = $this->createForm(MetaPurposeForm::class, $resourcePurpose);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_meta_purpose_index', [
                'id' => $resourcePurpose->getId(),
            ]);
        }

        return $this->render('moderator/meta/purpose/edit.html.twig', [
            'resource_purpose' => $resourcePurpose,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_meta_purpose_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MetaPurpose $resourcePurpose): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resourcePurpose->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resourcePurpose);
            $entityManager->flush();
        }

        return $this->redirectToRoute('moderator_meta_purpose_index');
    }
}
