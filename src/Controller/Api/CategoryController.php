<?php

namespace App\Controller\Api;

use App\Entity\MetaCategory;
use App\Repository\MetaCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class CategoryController extends AbstractController
{

    /**
     * @Route("/category", methods={"GET"}, name="api_resource_category")
     */
    public function category(MetaCategoryRepository $nestedTreeRepository): Response
    {

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

        return new JsonResponse(
            json_encode($categoriesArray, JSON_UNESCAPED_UNICODE ^ JSON_PRETTY_PRINT),
            200,
            [],
            true
        );

    }

    /**
     * @Route("/tree", methods={"GET"}, name="api_resource_tree")
     */
    public function tree(MetaCategoryRepository $nestedTreeRepository): Response
    {

        $tree = $nestedTreeRepository->childrenHierarchy(null,false,array());
        return new JsonResponse(
            json_encode($tree, JSON_UNESCAPED_UNICODE ^ JSON_PRETTY_PRINT),
            200,
            [],
            true
        );

    }

}
