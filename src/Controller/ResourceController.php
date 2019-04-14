<?php

namespace App\Controller;

use App\Entity\Resource;
use App\Entity\ResourceKeyword;
use App\Repository\ResourceKeywordRepository;
use App\Repository\ResourceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
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
    public function index(
        Request $request,
        int $page,
        string $_format,
        ResourceRepository $resourceRepository,
        ResourceKeywordRepository $keywordRepository
    ): Response
    {
        $keyword = null;
        if ($request->query->has('keyword')) {
            /** @var ResourceKeyword|null $keyword */
            $keyword = $keywordRepository->findOneBy(['name' => $request->query->get('keyword')]);
        }
        $latestResources = $resourceRepository->findLatest($page, $keyword);

        // Every template name also has two extensions that specify the format and
        // engine for that template.
        // See https://symfony.com/doc/current/templating.html#template-suffix
        return $this->render('resource/index.'.$_format.'.twig', ['list' => $latestResources]);
    }

    /**
     * @Route("/resource/{id}", methods={"GET"}, name="resource_item")
     *
     * NOTE: The $post controller argument is automatically injected by Symfony
     * after performing a database query looking for a Post with the 'slug'
     * value given in the route.
     * See https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
     */
    public function postShow(Resource $resource): Response
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
     * @Route("/search", methods={"GET"}, name="resource_search")
     */
    public function search(Request $request, ResourceRepository  $repository): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->render('resource/search.html.twig');
        }

        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);
        $foundResources = $repository->findBySearchQuery($query, $limit);

        $results = [];
        foreach ($foundResources as $resource) {
            $results[] = [
                'title' => htmlspecialchars($resource->getTitle(), ENT_COMPAT | ENT_HTML5),
                'date' => $resource->getCreatedAt()->format('M d, Y'),
                'author' => htmlspecialchars($resource->getAuthor()->getFullName(), ENT_COMPAT | ENT_HTML5),
                'summary' => htmlspecialchars($resource->getAnnotation(), ENT_COMPAT | ENT_HTML5),
                'url' => $this->generateUrl('resource_item', ['id' => $resource->getId()]),
            ];
        }

        return $this->json($results);
    }
}
