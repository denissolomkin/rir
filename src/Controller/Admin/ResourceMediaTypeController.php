<?php

namespace App\Controller\Admin;

use App\Entity\ResourceExtension;
use App\Entity\ResourceMediaType;
use App\Form\ResourceMediaTypeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/attribute/media-type")
 * @IsGranted("ROLE_ADMIN")
 */
class ResourceMediaTypeController extends AbstractController
{

    /**
     * @Route("/", methods={"GET"}, name="admin_resource_media_type_index")
     */
    public function relation(EntityManagerInterface $entityManager): Response
    {
        $mediaTypes = $entityManager->getRepository(ResourceMediaType::class)->findAll();

        return $this->render('admin/resource/media_type/list.html.twig', [
            'list' => $mediaTypes,
        ]);
    }

    /**
     * @Route("/save", methods={"POST"}, name="admin_resource_media_type_save")
     */
    public function save(EntityManagerInterface $entityManager, Request $request): Response
    {

        $types = $request->request->get('types', []);
        foreach ($types as $typeId => $extensions) {
            $mediaType = $entityManager->getRepository(ResourceMediaType::class)->find($typeId);
            foreach ($extensions as $extension) {
                $mediaType->addExtension(
                    $entityManager->getRepository(ResourceExtension::class)->find($extension)
                );
            }
            $entityManager->persist($mediaType);
        }

        $entityManager->flush();

        return $this->json([]);
    }

    /**
     * @Route("/", name="resource_media_type_index", methods={"GET"})
     */
    public function index(): Response
    {
        $resourceMediaTypes = $this->getDoctrine()
            ->getRepository(ResourceMediaType::class)
            ->findAll();

        return $this->render('admin/attribute/resource_media_type/index.html.twig', [
            'resource_media_types' => $resourceMediaTypes,
        ]);
    }

    /**
     * @Route("/new", name="resource_media_type_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $resourceMediaType = new ResourceMediaType();
        $form = $this->createForm(ResourceMediaTypeType::class, $resourceMediaType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resourceMediaType);
            $entityManager->flush();

            return $this->redirectToRoute('resource_media_type_index');
        }

        return $this->render('admin/attribute/resource_media_type/new.html.twig', [
            'resource_media_type' => $resourceMediaType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resource_media_type_show", methods={"GET"})
     */
    public function show(ResourceMediaType $resourceMediaType): Response
    {
        return $this->render('admin/attribute/resource_media_type/show.html.twig', [
            'resource_media_type' => $resourceMediaType,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="resource_media_type_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ResourceMediaType $resourceMediaType): Response
    {
        $form = $this->createForm(ResourceMediaTypeType::class, $resourceMediaType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('resource_media_type_index', [
                'id' => $resourceMediaType->getId(),
            ]);
        }

        return $this->render('admin/attribute/resource_media_type/edit.html.twig', [
            'resource_media_type' => $resourceMediaType,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resource_media_type_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ResourceMediaType $resourceMediaType): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resourceMediaType->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resourceMediaType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('resource_media_type_index');
    }
}
