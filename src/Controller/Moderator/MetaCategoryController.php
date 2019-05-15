<?php

namespace App\Controller\Moderator;

use App\Entity\MetaCategory;
use App\Form\MetaCategoryType;
use App\Repository\MetaCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderator/meta/category")
 */
class MetaCategoryController extends AbstractController
{
    /**
     * @Route("/", name="meta_category_index", methods={"GET"})
     */
    public function index(MetaCategoryRepository $nestedTreeRepository): Response
    {

        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function ($node) {
                return '<a href="/page/' . $node['id'] . '">' . $node['name'] . '</a>';
            }
        );

        $htmlTree = $nestedTreeRepository->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* true: load all children, false: only direct */
            $options
        );

        $categories = $nestedTreeRepository->findAll();
        $categoriesArray = [];
        /** @var MetaCategory $category */
        foreach ($categories as $category) {
            $categoriesArray[] = [
                'id' => $category->getId(),
                'parent' => $category->getParent()?$category->getParent()->getId():'#',
                'text' => $category->getName()
            ];
        }

        return $this->render('moderator/meta/category/index.html.twig', [
            'tree' => $htmlTree,
            'json_categories' => json_encode($categoriesArray),
            'meta_categories' => $nestedTreeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="meta_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $metaCategory = new MetaCategory();
        $form = $this->createForm(MetaCategoryType::class, $metaCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($metaCategory);
            $entityManager->flush();

            return $this->redirectToRoute('meta_category_index');
        }

        return $this->render('moderator/meta/category/new.html.twig', [
            'meta_category' => $metaCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="meta_category_show", methods={"GET"})
     */
    public function show(MetaCategory $metaCategory): Response
    {
        return $this->render('moderator/meta/category/show.html.twig', [
            'meta_category' => $metaCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="meta_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MetaCategory $metaCategory): Response
    {
        $form = $this->createForm(MetaCategoryType::class, $metaCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('meta_category_index', [
                'id' => $metaCategory->getId(),
            ]);
        }

        return $this->render('moderator/meta/category/edit.html.twig', [
            'meta_category' => $metaCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="meta_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MetaCategory $metaCategory): Response
    {
        if ($this->isCsrfTokenValid('delete' . $metaCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($metaCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('meta_category_index');
    }
}
