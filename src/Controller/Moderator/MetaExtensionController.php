<?php

namespace App\Controller\Moderator;

use App\Entity\MetaExtension;
use App\Form\MetaExtensionForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderator/meta/extension")
 * @IsGranted("ROLE_MODERATOR")
 */
class MetaExtensionController extends AbstractController
{
    /**
     * @Route("/", name="moderator_meta_extension_index", methods={"GET"})
     */
    public function index(): Response
    {
        $resourceExtensions = $this->getDoctrine()
            ->getRepository(MetaExtension::class)
            ->findAll();

        return $this->render('moderator/meta/extension/index.html.twig', [
            'resource_extensions' => $resourceExtensions,
        ]);
    }

    /**
     * @Route("/new", name="moderator_meta_extension_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $resourceExtension = new MetaExtension();
        $form = $this->createForm(MetaExtensionForm::class, $resourceExtension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resourceExtension);
            $entityManager->flush();

            return $this->redirectToRoute('moderator_meta_extension_index');
        }

        return $this->render('moderator/meta/extension/new.html.twig', [
            'resource_extension' => $resourceExtension,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_meta_extension_show", methods={"GET"})
     */
    public function show(MetaExtension $resourceExtension): Response
    {
        return $this->render('moderator/meta/extension/show.html.twig', [
            'resource_extension' => $resourceExtension,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="moderator_meta_extension_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MetaExtension $resourceExtension): Response
    {
        $form = $this->createForm(MetaExtensionForm::class, $resourceExtension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_meta_extension_index', [
                'id' => $resourceExtension->getId(),
            ]);
        }

        return $this->render('moderator/meta/extension/edit.html.twig', [
            'resource_extension' => $resourceExtension,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_meta_extension_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MetaExtension $resourceExtension): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resourceExtension->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resourceExtension);
            $entityManager->flush();
        }

        return $this->redirectToRoute('moderator_meta_extension_index');
    }
}
