<?php

namespace App\Controller\Admin;

use App\Entity\ResourceExtension;
use App\Form\ResourceExtensionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/attribute/extension")
 * @IsGranted("ROLE_ADMIN")
 */
class ResourceExtensionController extends AbstractController
{
    /**
     * @Route("/", name="resource_extension_index", methods={"GET"})
     */
    public function index(): Response
    {
        $resourceExtensions = $this->getDoctrine()
            ->getRepository(ResourceExtension::class)
            ->findAll();

        return $this->render('resource_extension/index.html.twig', [
            'resource_extensions' => $resourceExtensions,
        ]);
    }

    /**
     * @Route("/new", name="resource_extension_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $resourceExtension = new ResourceExtension();
        $form = $this->createForm(ResourceExtensionType::class, $resourceExtension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resourceExtension);
            $entityManager->flush();

            return $this->redirectToRoute('resource_extension_index');
        }

        return $this->render('resource_extension/new.html.twig', [
            'resource_extension' => $resourceExtension,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resource_extension_show", methods={"GET"})
     */
    public function show(ResourceExtension $resourceExtension): Response
    {
        return $this->render('resource_extension/show.html.twig', [
            'resource_extension' => $resourceExtension,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="resource_extension_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ResourceExtension $resourceExtension): Response
    {
        $form = $this->createForm(ResourceExtensionType::class, $resourceExtension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('resource_extension_index', [
                'id' => $resourceExtension->getId(),
            ]);
        }

        return $this->render('resource_extension/edit.html.twig', [
            'resource_extension' => $resourceExtension,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resource_extension_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ResourceExtension $resourceExtension): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resourceExtension->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resourceExtension);
            $entityManager->flush();
        }

        return $this->redirectToRoute('resource_extension_index');
    }
}
