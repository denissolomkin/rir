<?php

namespace App\Controller\Moderator;

use App\Entity\MetaKeyword;
use App\Form\MetaKeywordForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("moderator/meta/keyword")
 * @IsGranted("ROLE_MODERATOR")
 */
class MetaKeywordController extends AbstractController
{
    /**
     * @Route("/", name="moderator_meta_keyword_index", methods={"GET"})
     */
    public function index(): Response
    {
        $resourceKeywords = $this->getDoctrine()
            ->getRepository(MetaKeyword::class)
            ->findAll();

        return $this->render('moderator/meta/keyword/index.html.twig', [
            'resource_keywords' => $resourceKeywords,
        ]);
    }

    /**
     * @Route("/new", name="moderator_meta_keyword_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $resourceKeyword = new MetaKeyword();
        $form = $this->createForm(MetaKeywordForm::class, $resourceKeyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resourceKeyword);
            $entityManager->flush();

            return $this->redirectToRoute('moderator_meta_keyword_index');
        }

        return $this->render('moderator/meta/keyword/new.html.twig', [
            'resource_keyword' => $resourceKeyword,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_meta_keyword_show", methods={"GET"})
     */
    public function show(MetaKeyword $resourceKeyword): Response
    {
        return $this->render('moderator/meta/keyword/show.html.twig', [
            'resource_keyword' => $resourceKeyword,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="moderator_meta_keyword_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MetaKeyword $resourceKeyword): Response
    {
        $form = $this->createForm(MetaKeywordForm::class, $resourceKeyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('moderator_meta_keyword_index', [
                'id' => $resourceKeyword->getId(),
            ]);
        }

        return $this->render('moderator/meta/keyword/edit.html.twig', [
            'resource_keyword' => $resourceKeyword,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="moderator_meta_keyword_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MetaKeyword $resourceKeyword): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resourceKeyword->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resourceKeyword);
            $entityManager->flush();
        }

        return $this->redirectToRoute('moderator_meta_keyword_index');
    }
}
