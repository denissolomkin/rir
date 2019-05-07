<?php

namespace App\Controller\Admin;

use App\Entity\ResourceKeyword;
use App\Form\ResourceKeywordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/attribute/keyword")
 * @IsGranted("ROLE_ADMIN")
 */
class ResourceKeywordController extends AbstractController
{
    /**
     * @Route("/", name="resource_keyword_index", methods={"GET"})
     */
    public function index(): Response
    {
        $resourceKeywords = $this->getDoctrine()
            ->getRepository(ResourceKeyword::class)
            ->findAll();

        return $this->render('resource_keyword/index.html.twig', [
            'resource_keywords' => $resourceKeywords,
        ]);
    }

    /**
     * @Route("/new", name="resource_keyword_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $resourceKeyword = new ResourceKeyword();
        $form = $this->createForm(ResourceKeywordType::class, $resourceKeyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resourceKeyword);
            $entityManager->flush();

            return $this->redirectToRoute('resource_keyword_index');
        }

        return $this->render('resource_keyword/new.html.twig', [
            'resource_keyword' => $resourceKeyword,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resource_keyword_show", methods={"GET"})
     */
    public function show(ResourceKeyword $resourceKeyword): Response
    {
        return $this->render('resource_keyword/show.html.twig', [
            'resource_keyword' => $resourceKeyword,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="resource_keyword_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ResourceKeyword $resourceKeyword): Response
    {
        $form = $this->createForm(ResourceKeywordType::class, $resourceKeyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('resource_keyword_index', [
                'id' => $resourceKeyword->getId(),
            ]);
        }

        return $this->render('resource_keyword/edit.html.twig', [
            'resource_keyword' => $resourceKeyword,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resource_keyword_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ResourceKeyword $resourceKeyword): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resourceKeyword->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resourceKeyword);
            $entityManager->flush();
        }

        return $this->redirectToRoute('resource_keyword_index');
    }
}
