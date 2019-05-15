<?php

namespace App\Controller\Author;

use App\Entity\MetaKeyword;
use App\Repository\MetaKeywordRepository;
use App\Repository\ResourceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class IndexController extends AbstractController
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
        return $this->render('user/resource/index.' . $_format . '.twig', ['list' => $latestResources]);
    }

}
