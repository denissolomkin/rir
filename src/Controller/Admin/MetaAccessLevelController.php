<?php

namespace App\Controller\Admin;

use App\Entity\MetaAccessLevel;
use App\Form\MetaAccessLevelForm;
use App\Repository\MetaAccessLevelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * Creates a new ResourceAccessLevel entity.
     *
     * @Route("/new", methods={"GET", "POST"}, name="admin_meta_access_level_new")
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function new(Request $request): Response
    {
        $object = new MetaAccessLevel();

        $form = $this->createForm(MetaAccessLevelForm::class, $object)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            $this->addFlash('success', 'resource.access_level.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_meta_access_level_new');
            }

            return $this->redirectToRoute('admin_meta_access_level_index');
        }

        return $this->render('admin/meta/access_level/new.html.twig', [
            'item' => $object,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing ResourceAccessLevel entity.
     *
     * @Route("/{id<\d+>}/edit",methods={"GET", "POST"}, name="admin_meta_access_level_edit")
     */
    public function edit(Request $request, MetaAccessLevel $object): Response
    {
        $form = $this->createForm(MetaAccessLevelForm::class, $object);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'resource.access_level.updated_successfully');

            return $this->redirectToRoute('admin_meta_access_level_edit', ['id' => $object->getId()]);
        }

        return $this->render('admin/meta/access_level/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $object,
        ]);
    }

    /**
     * Deletes a ResourceAccessLevel entity.
     *
     * @Route("/{id}/delete", methods={"POST"}, name="admin_meta_access_level_delete")
     */
    public function delete(Request $request, MetaAccessLevel $object): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_meta_access_level_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        $this->addFlash('success', 'resource.access_level.deleted_successfully');

        return $this->redirectToRoute('admin_meta_access_level_index');
    }
}
