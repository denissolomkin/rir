<?php

namespace App\Controller\Api;

use App\Entity\Resource;
use App\Entity\SearchResource;
use App\Entity\User;
use App\Form\SearchResourceType;
use App\Repository\ResourceRepository;
use App\Repository\SearchResourceRepository;
use App\Utils\EntityExporter;
use App\Utils\FormExporter;
use App\Utils\SearchFormPreparator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/api/")
 */
class SearchController extends AbstractController
{

    /**
     * @Route("form", methods={"GET","POST"}, name="api_resource_form")
     */
    public function form(
        Request $request,
        SearchFormPreparator $formPreparator,
        TokenStorageInterface $tokenStorage,
        SearchResourceRepository $searchResourceRepository
    ): Response
    {

        /** @var User $user */
        $searchResource = $searchResourceRepository->find(
                $tokenStorage->getToken()->getUser() instanceof UserInterface
                ?? $tokenStorage->getToken()->getUser()->getId()
            ) ?? new SearchResource();

        $form = $this->createForm(SearchResourceType::class, $searchResource);

        $form->handleRequest($request);

        $formView = $form->createView();
        $exporter = new FormExporter($formView);
        $result = $formPreparator->prepare($exporter->export());

        return new JsonResponse($result, 200, [], true);

    }


    /**
     * @Route("search", methods={"GET","POST"}, name="api_resource_search")
     */
    public function search(
        Request $request,
        ResourceRepository $repository
    ): Response
    {

        $searchResource = new SearchResource();
        $form = $this->createForm(SearchResourceType::class, $searchResource);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() /*&& $form->isValid()*/) {

            //$em = $this->getDoctrine()->getManager();
            //$em->persist($searchResource);
            //$em->flush();

            $results = array_map(function ($e) {
                return (new EntityExporter())->convert($e);
            }, $repository->findBySearch($searchResource));


        } else {
            return new JsonResponse(['error' => 'Please, first submit form'], 400);
        }

        return $this->json($results, 200, [], ['json_encode_options' =>
            JSON_UNESCAPED_UNICODE ^ JSON_PRETTY_PRINT]);

    }

    /**
     * @Route("quick-search", methods={"GET"}, name="api_resource_quick_search", condition="request.isXmlHttpRequest()")
     */
    public function quickSearch(
        Request $request,
        ResourceRepository $repository
    ): Response
    {

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
