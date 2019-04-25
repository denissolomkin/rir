<?php

namespace App\Controller\Admin;

use App\Entity\ResourceExtension;
use App\Entity\ResourceMediaType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/resource/media-type")
 * @IsGranted("ROLE_ADMIN")
 */
class ResourceMediaTypeController extends AbstractController
{

    /**
     * @Route("/", methods={"GET"}, name="admin_resource_media_type_index")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $mediaTypes = $entityManager->getRepository(ResourceMediaType::class)->findAll();

        return $this->render('admin/resource/media_type/list.html.twig', [
            'list' => $mediaTypes,
        ]);
    }

    /**
     * @Route("/save", methods={"POST"}, name="admin_resource_media_type_save")
     */
    public function save(EntityManagerInterface $entityManager, Request $request): Response
    {

        $types = $request->request->get('types', []);
        foreach ($types as $typeId => $extensions) {
            $mediaType = $entityManager->getRepository(ResourceMediaType::class)->find($typeId);
            foreach ($extensions as $extension) {
                $mediaType->addExtension(
                    $entityManager->getRepository(ResourceExtension::class)->find($extension)
                );
            }
            $entityManager->persist($mediaType);
        }

        $entityManager->flush();

        return $this->json([]);
    }
}
