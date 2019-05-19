<?php

namespace App\Controller\Resource;

use App\Entity\MetaKeyword;
use App\Entity\Resource;
use App\Repository\MetaKeywordRepository;
use App\Repository\ResourceRepository;
use App\Utils\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use App\Entity\Search;
use App\Entity\User;
use App\Form\SearchByUserForm;
use App\Repository\SearchResourceRepository;
use App\Utils\SearchFormPreparator;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * @Route("/resource/")
 */
class ResourceController extends AbstractController
{
    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="resource_index")
     * @Route("/rss.xml", defaults={"page": "1", "_format"="xml"}, methods={"GET"}, name="resource_rss")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="resource_index_paginated")
     * @Cache(smaxage="10")
     *
     * NOTE: For standard formats, Symfony will also automatically choose the best
     * Content-Type header for the response.
     * See https://symfony.com/doc/current/quick_tour/the_controller.html#using-formats
     */
    public function list(
        Request $request,
        int $page,
        string $_format,
        ResourceRepository $resourceRepository,
        MetaKeywordRepository $keywordRepository
    ): Response
    {
        $keyword = null;
        if ($request->query->has('keyword')) {
            /** @var MetaKeyword|null $keyword */
            $keyword = $keywordRepository->findOneBy(['name' => $request->query->get('keyword')]);
        }

        $latestResources = $resourceRepository->findLatest($page, $keyword);

        // Every template name also has two extensions that specify the format and
        // engine for that template.
        // See https://symfony.com/doc/current/templating.html#template-suffix
        return $this->render('resource/index.' . $_format . '.twig', ['list' => $latestResources]);
    }

    /**
     * @Route("{id}/file/{fileName}", methods={"GET"}, name="resource_download", requirements={"id"="\d+"})
     */
    public function download(Resource $resource, FileUploader $fileUploader)
    {

        $file = $resource->getFile();
        $filePath = sprintf('%s/%s/%s', $fileUploader->getTargetDirectory(), $file->getExtension(), $file->getUpload());

        return $this->file($filePath, $file->getFileName(), ResponseHeaderBag::DISPOSITION_INLINE);

    }


    /**
     * @Route("{id}", methods={"GET"}, name="resource_item", requirements={"id"="\d+"})
     *
     * NOTE: The $post controller argument is automatically injected by Symfony
     * after performing a database query looking for a Post with the 'slug'
     * value given in the route.
     * See https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
     */
    public function item(Resource $resource): Response
    {
        // Symfony's 'dump()' function is an improved version of PHP's 'var_dump()' but
        // it's not available in the 'prod' environment to prevent leaking sensitive information.
        // It can be used both in PHP files and Twig templates, but it requires to
        // have enabled the DebugBundle. Uncomment the following line to see it in action:
        //
        // dump($post, $this->getUser(), new \DateTime());

        return $this->render('resource/item.html.twig', ['item' => $resource]);
    }


    /**
     * @Route("search", methods={"GET","POST"}, name="resource_search", condition="!request.isXmlHttpRequest()")
     */
    public function search(
        Request $request,
        ResourceRepository $repository,
        SearchResourceRepository $searchResourceRepository,
        TokenStorageInterface $tokenStorage
    ): Response
    {
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();

        $search = $searchResourceRepository->findOneBy(['user'=>$user->getId()]) ?? new Search();

        $form = $this->createForm(
            SearchByUserForm::class,
            $search, [
            'action' => $this->generateUrl('resource_search')
        ]);

        $form->handleRequest($request);
        $result = null;


        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {

            $search->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($search);
            $em->flush();

            /** @var Collection $result */
            $result = $repository->findBySearch($search);
        }

        return $this->render('user/resource/search.html.twig',
            [
                'form' => $form->createView(),
                'list' => $result
            ]);
    }
}
