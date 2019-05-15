<?php

namespace App\Controller\Admin;

use App\Entity\MetaAccessLevel;
use App\Form\MetaAccessLevelForm;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @Route("/", methods={"GET"}, name="admin_resource_access_level_index")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $list = $entityManager->getRepository(MetaAccessLevel::class)->findAll();

        return $this->render('moderator/attribute/access_level/list.html.twig', ['list' => $list]);
    }

    /**
     * Creates a new ResourceAccessLevel entity.
     *
     * @Route("/new", methods={"GET", "POST"}, name="admin_resource_access_level_new")
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
                return $this->redirectToRoute('admin_resource_access_level_new');
            }

            return $this->redirectToRoute('admin_resource_access_level_index');
        }

        return $this->render('admin/resource/access_level/new.html.twig', [
            'item' => $object,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing ResourceAccessLevel entity.
     *
     * @Route("/{id<\d+>}/edit",methods={"GET", "POST"}, name="admin_resource_access_level_edit")
     */
    public function edit(Request $request, MetaAccessLevel $object): Response
    {
        $form = $this->createForm(MetaAccessLevelForm::class, $object);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'resource.access_level.updated_successfully');

            return $this->redirectToRoute('admin_resource_access_level_edit', ['id' => $object->getId()]);
        }

        return $this->render('admin/resource/access_level/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $object,
        ]);
    }

    /**
     * Deletes a ResourceAccessLevel entity.
     *
     * @Route("/{id}/delete", methods={"POST"}, name="admin_resource_access_level_delete")
     */
    public function delete(Request $request, MetaAccessLevel $object): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_resource_access_level_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        $this->addFlash('success', 'resource.access_level.deleted_successfully');

        return $this->redirectToRoute('admin_resource_access_level_index');
    }
}
