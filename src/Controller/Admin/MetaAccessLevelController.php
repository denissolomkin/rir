<?php

namespace App\Controller\Admin;

use App\Entity\MetaAccessLevel;
use App\Repository\MetaAccessLevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/meta/access-level")
 */
class MetaAccessLevelController extends AbstractController
{
    /**
     * Lists all ResourceAccessLevel entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'admin_post_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *
     * @Route("/", methods={"GET"}, name="admin_meta_access_level_index")
     */
    public function index(
        EntityManagerInterface $entityManager,
        MetaAccessLevelRepository $nestedTreeRepository
    ): Response
    {
        var_dump($nestedTreeRepository->verify());

        // $nestedTreeRepository->recover();
        // $entityManager->flush();

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

        $list = $nestedTreeRepository->findAll();
        $data = [];
        /** @var MetaAccessLevel $node */
        foreach ($list as $node) {
            $data[] = [
                'id' => $node->getId(),
                'parent' => $node->getParent() ? $node->getParent()->getId() : '#',
                'text' => $node->getName()
            ];
        }

        return $this->render('admin/meta/access_level/list.html.twig', [
            'tree' => $htmlTree,
            'tree_data' => json_encode($data, JSON_UNESCAPED_UNICODE ^ JSON_PRETTY_PRINT),
        ]);

    }

    /**
     * @Route("/{operation}", name="moderator_meta_access_level_operation", methods={"POST"}, requirements={"operation"="\D\w+"})
     */
    public function operation(
        $operation,
        Request $request,
        MetaAccessLevelRepository $nestedTreeRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {

        $node = $request->request->all();

        if (is_numeric($node['id'])) {
            /** @var MetaAccessLevel $node */
            $node = $nestedTreeRepository->find((int)$node['id']);
        } else {
            $node = new MetaAccessLevel();
            $node->setName($node['text']);
        }

        if (is_numeric($node['parent'])) {
            /** @var MetaAccessLevel $parent */
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
