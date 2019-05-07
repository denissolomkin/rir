<?php

namespace App\Controller\Admin;

use App\Entity\ResourceDocumentType;
use App\Form\ResourceDocumentTypeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/attribute/document-type")
 * @IsGranted("ROLE_ADMIN")
 */
class ResourceDocumentTypeController extends AbstractController
{
    /**
     * @Route("/", name="resource_document_type_index", methods={"GET"})
     */
    public function index(): Response
    {
        $resourceDocumentTypes = $this->getDoctrine()
            ->getRepository(ResourceDocumentType::class)
            ->findAll();

        return $this->render('admin/attribute/resource_document_type/index.html.twig', [
            'resource_document_types' => $resourceDocumentTypes,
        ]);
    }

    /**
     * @Route("/new", name="resource_document_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $resourceDocumentType = new ResourceDocumentType();
        $form = $this->createForm(ResourceDocumentTypeType::class, $resourceDocumentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resourceDocumentType);
            $entityManager->flush();

            return $this->redirectToRoute('resource_document_type_index');
        }

        return $this->render('admin/attribute/resource_document_type/new.html.twig', [
            'resource_document_type' => $resourceDocumentType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resource_document_type_show", methods={"GET"})
     */
    public function show(ResourceDocumentType $resourceDocumentType): Response
    {
        return $this->render('admin/attribute/resource_document_type/show.html.twig', [
            'resource_document_type' => $resourceDocumentType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="resource_document_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ResourceDocumentType $resourceDocumentType): Response
    {
        $form = $this->createForm(ResourceDocumentTypeType::class, $resourceDocumentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('resource_document_type_index', [
                'id' => $resourceDocumentType->getId(),
            ]);
        }

        return $this->render('admin/attribute/resource_document_type/edit.html.twig', [
            'resource_document_type' => $resourceDocumentType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resource_document_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ResourceDocumentType $resourceDocumentType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resourceDocumentType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resourceDocumentType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('resource_document_type_index');
    }
}
