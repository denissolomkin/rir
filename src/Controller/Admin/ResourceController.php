<?php

namespace App\Controller\Admin;

use App\Entity\Resource;
use App\Form\ResourceType;
use App\Utils\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/resource")
 * @IsGranted("ROLE_ADMIN")
 */
class ResourceController extends AbstractController
{
    /**
     * Lists all Resource entities.
     *
     * This controller responds to two different routes with the same URL:
     *   * 'admin_post_index' is the route with a name that follows the same
     *     structure as the rest of the controllers of this class.
     *
     * @Route("/", methods={"GET"}, name="admin_index")
     * @Route("/", methods={"GET"}, name="admin_resource_index")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $list = $entityManager->getRepository(Resource::class)->findAll();

        return $this->render('admin/resource/list.html.twig', ['list' => $list]);
    }

    /**
     * @Route("/drug-and-drop", methods={"GET", "POST"}, name="admin_resource_drug_and_drop")
     */
    public function drugAndDrop(EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/resource/drug-and-drop.html.twig');
    }

    /**
     * Creates a new Resource entity.
     *
     * @Route("/new", methods={"GET", "POST"}, name="admin_resource_new")
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function new(Request $request): Response
    {
        $object = new Resource();
        $object->setAuthor($this->getUser());

        $form = $this->createForm(ResourceType::class, $object)
            ->add('saveAndPublish', SubmitType::class)
            ->add('saveAsDraft', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('saveAsDraft')->isClicked()) {
                $object->setStatus(Resource::STATUS_DRAFT);
            } elseif ($form->get('saveAndPublish')->isClicked()) {
                $object
                    ->setStatus(Resource::STATUS_ON_MODERATION)
                    ->setPublishedAt(new \DateTime());
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            $this->addFlash('success', 'resource.created_successfully');


            return $this->redirectToRoute('admin_resource_index');
        }

        return $this->render('admin/resource/new.html.twig', [
            'item' => $object,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Displays a form to edit an existing Resource entity.
     *
     * @Route("/{id<\d+>}/edit",methods={"GET", "POST"}, name="admin_resource_edit")
     */
    public function edit(Request $request, Resource $object): Response
    {
        $form = $this->createForm(ResourceType::class, $object)
            ->add('save', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $object->setEditedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'resource.updated_successfully');

            return $this->redirectToRoute('admin_resource_edit', ['id' => $object->getId()]);
        }

        return $this->render('admin/resource/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $object,
        ]);
    }

    /**
     * publish an existing Resource entity.
     *
     * @Route("/{id<\d+>}/publish",methods={"GET", "POST"}, name="admin_resource_publish")
     */
    public function publish(Request $request, Resource $object): Response
    {

        if ($object->getStatus() === Resource::STATUS_DRAFT) {

            $object
                ->setStatus(Resource::STATUS_ON_MODERATION)
                ->setPublishedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            $this->addFlash('success', 'resource.published_successfully');
        }

        return $this->redirectToRoute('admin_resource_index');

    }

    /**
     * approve an existing Resource entity.
     *
     * @Route("/{id<\d+>}/approve",methods={"GET", "POST"}, name="admin_resource_approve")
     */
    public function approve(Request $request, Resource $object): Response
    {

        if ($object->getStatus() === Resource::STATUS_ON_MODERATION) {

            $object
                ->setStatus(Resource::STATUS_PUBLISHED)
                ->setApprovedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            $this->addFlash('success', 'resource.approved_successfully');
        }

        return $this->redirectToRoute('admin_resource_index');

    }

    /**
     * Deletes a Resource entity.
     *
     * @Route("/{id}/delete", methods={"POST"}, name="admin_resource_delete")
     */
    public function delete(Request $request, Resource $object): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_resource_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        $this->addFlash('success', 'resource.deleted_successfully');

        return $this->redirectToRoute('admin_resource_index');
    }
}
