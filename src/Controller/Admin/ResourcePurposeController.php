<?php

namespace App\Controller\Admin;

use App\Entity\ResourcePurpose;
use App\Form\ResourcePurposeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/attribute/purpose")
 * @IsGranted("ROLE_ADMIN")
 */
class ResourcePurposeController extends AbstractController
{
    /**
     * @Route("/", name="resource_purpose_index", methods={"GET"})
     */
    public function index(): Response
    {
        $resourcePurposes = $this->getDoctrine()
            ->getRepository(ResourcePurpose::class)
            ->findAll();

        return $this->render('resource_purpose/index.html.twig', [
            'resource_purposes' => $resourcePurposes,
        ]);
    }

    /**
     * @Route("/new", name="resource_purpose_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $resourcePurpose = new ResourcePurpose();
        $form = $this->createForm(ResourcePurposeType::class, $resourcePurpose);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resourcePurpose);
            $entityManager->flush();

            return $this->redirectToRoute('resource_purpose_index');
        }

        return $this->render('resource_purpose/new.html.twig', [
            'resource_purpose' => $resourcePurpose,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resource_purpose_show", methods={"GET"})
     */
    public function show(ResourcePurpose $resourcePurpose): Response
    {
        return $this->render('resource_purpose/show.html.twig', [
            'resource_purpose' => $resourcePurpose,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="resource_purpose_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ResourcePurpose $resourcePurpose): Response
    {
        $form = $this->createForm(ResourcePurposeType::class, $resourcePurpose);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('resource_purpose_index', [
                'id' => $resourcePurpose->getId(),
            ]);
        }

        return $this->render('resource_purpose/edit.html.twig', [
            'resource_purpose' => $resourcePurpose,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resource_purpose_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ResourcePurpose $resourcePurpose): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resourcePurpose->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resourcePurpose);
            $entityManager->flush();
        }

        return $this->redirectToRoute('resource_purpose_index');
    }
}
