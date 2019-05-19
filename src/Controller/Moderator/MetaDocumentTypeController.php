<?php

namespace App\Controller\Moderator;

use App\Entity\MetaDocumentType;
use App\Form\MetaDocumentTypeForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderator/meta/document-type")
 */
class MetaDocumentTypeController extends AbstractController
{
    /**
     * @Route("/", name="moderator_meta_document_type_index", methods={"GET"})
     */
    public function index(): Response
    {
        $resourceDocumentTypes = $this->getDoctrine()
            ->getRepository(MetaDocumentType::class)
            ->findAll();

        return $this->render('moderator/meta/document_type/index.html.twig', [
            'resource_document_types' => $resourceDocumentTypes,
        ]);
    }

    /**
     * @Route("/new", name="moderator_meta_document_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $documentType = new MetaDocumentType();
        $form = $this->createForm(MetaDocumentTypeForm::class, $documentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($documentType);
            $entityManager->flush();

            return $this->redirectToRoute('moderator_meta_document_type_index');
        }

        return $this->render('moderator/meta/document_type/new.html.twig', [
            'resource_document_type' => $documentType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_meta_document_type_show", methods={"GET"})
     */
    public function show(MetaDocumentType $documentType): Response
    {
        return $this->render('moderator/meta/document_type/show.html.twig', [
            'resource_document_type' => $documentType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="moderator_meta_document_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MetaDocumentType $documentType): Response
    {
        $form = $this->createForm(MetaDocumentTypeForm::class, $documentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_meta_document_type_index', [
                'id' => $documentType->getId(),
            ]);
        }

        return $this->render('moderator/meta/document_type/edit.html.twig', [
            'resource_document_type' => $documentType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_meta_document_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MetaDocumentType $resourceDocumentType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resourceDocumentType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resourceDocumentType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('moderator_meta_document_type_index');
    }
}
