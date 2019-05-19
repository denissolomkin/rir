<?php

namespace App\Controller\Moderator;

use App\Entity\MetaCategory;
use App\Form\MetaCategoryType;
use App\Repository\MetaCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderator/meta/category")
 */
class MetaCategoryController extends AbstractController
{
    /**
     * @Route("/", name="moderator_meta_category_index", methods={"GET"})
     */
    public function index(
        MetaCategoryRepository $nestedTreeRepository,
        EntityManagerInterface $entityManager
    ): Response
    {

        var_dump($nestedTreeRepository->verify());

        $nestedTreeRepository->recover();
        $entityManager->flush();
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
                'parent' => $category->getParent() ? $category->getParent()->getId() : '#',
                'text' => $category->getName()
            ];
        }

        return $this->render('moderator/meta/category/index.html.twig', [
            'tree' => $htmlTree,
            'tree_url' => $this->generateUrl('api_resource_category'),
            'json_categories' => json_encode($categoriesArray),
            'meta_categories' => $nestedTreeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{operation}", name="moderator_meta_category_operation", methods={"POST"}, requirements={"operation"="\D\w+"})
     */
    public function operation(
        $operation,
        Request $request,
        MetaCategoryRepository $nestedTreeRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {

        $node = $request->request->all();

        if (is_numeric($node['id'])) {
            /** @var MetaCategory $node */
            $node = $nestedTreeRepository->find((int)$node['id']);
        } else {
            $node = new MetaCategory();
            $node->setName($node['text']);
        }

        if (is_numeric($node['parent'])) {
            /** @var MetaCategory $parent */
            $parent = $nestedTreeRepository->find((int)$node['parent']);
        } else {
            $parent = null;
        }

        $entityManager->persist($node);

        switch ($operation) {

            case "rename_node":
                $node->setName($node['text']);
                break;

            case "move_node":
                $node->setParent($parent);
                $nestedTreeRepository->persistAsLastChild($node);
                break;

            case "create_node":
                if ($parent) {
                    $nestedTreeRepository->persistAsLastChildOf($node, $parent);
                } else {
                    $nestedTreeRepository->persistAsFirstChild($node);
                }
                break;

            case "delete_node":
                $entityManager->remove($node);
                $nestedTreeRepository->reorder($node, 'name');
                $nestedTreeRepository->recover();
                break;

        }

        $entityManager->flush();

        return new JsonResponse(['id' => $node->getId()]);
    }

}
