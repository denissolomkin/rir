<?php

namespace App\Controller\Moderator;

use App\Entity\Resource;
use App\Entity\MetaExtension;
use App\Entity\File;
use App\Form\ResourceForm;
use App\Utils\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/moderator/resource")
 * @IsGranted("ROLE_MODERATOR")
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
     * @Route("/", methods={"GET"}, name="moderator_resource_index")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $list = $entityManager->getRepository(Resource::class)->findAll();

        return $this->render('moderator/resource/list.html.twig', ['list' => $list]);
    }


    private function prepareForm(
        Resource $object,
        Request $request,
        FileUploader $fileUploader,
        EntityManagerInterface $entityManager): FormInterface
    {

        $object->setAuthor($this->getUser());

        if ($request->files->has('upload')) {

            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $request->files->get('upload');

            $resourceFile = new File();
            $resourceFile
                ->setFilePath($file->getRealPath())
                ->setFileName($file->getClientOriginalName())
                ->setSize($file->getSize())
                ->setExtension($file->guessClientExtension())
                ->setUpload($fileUploader->upload($file));

            $entityManager->persist($resourceFile);
            $entityManager->flush();

            $object
                ->setFile($resourceFile)
                ->setSize($resourceFile->getSize())
                ->setTitle(str_replace(
                        '.' . $resourceFile->getExtension(),
                        '',
                        $resourceFile->getFileName())
                );

            $extension = $entityManager
                ->getRepository(MetaExtension::class)
                ->findOneBy(['name' => $resourceFile->getExtension()]);

            if ($extension) {

                if ($extension->getMediaType()) {
                    $object->setMediaType($extension->getMediaType());
                }

            } else {
                $extension = new MetaExtension();
                $extension->setName($resourceFile->getExtension());
                $entityManager->persist($extension);
                $entityManager->flush();
            }

            $object->setExtension($extension);
        }

        $form = $this->createForm(
            ResourceForm::class,
            $object,
            [
                'action' => $this->generateUrl('admin_resource_new')
            ]
        )
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

        }

        return $form;
    }

    /**
     * Displays a form to edit an existing Resource entity.
     *
     * @Route("/{id<\d+>}/edit",methods={"GET", "POST"}, name="moderator_resource_edit")
     */
    public function edit(Request $request, Resource $object): Response
    {
        $form = $this->createForm(ResourceForm::class, $object)
            ->add('save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $object->setEditedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'resource.updated_successfully');

            return $this->redirectToRoute('moderator_resource_edit', ['id' => $object->getId()]);
        }

        return $this->render('moderator/resource/edit.html.twig', [
            'form' => $form->createView(),
            'item' => $object,
        ]);
    }


    /**
     * approve an existing Resource entity.
     *
     * @Route("/{id<\d+>}/approve",methods={"GET", "POST"}, name="moderator_resource_approve")
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

        return $this->redirectToRoute('moderator_resource_index');

    }

    /**
     * Deletes a Resource entity.
     *
     * @Route("/{id}/delete", methods={"POST"}, name="moderator_resource_delete")
     */
    public function delete(Request $request, Resource $object): Response
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('moderator_resource_index');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($object);
        $em->flush();

        $this->addFlash('success', 'resource.deleted_successfully');

        return $this->redirectToRoute('moderator_resource_index');
    }
}
