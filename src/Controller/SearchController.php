<?php

namespace App\Controller;

use App\Entity\Resource;
use App\Entity\SearchResource;
use App\Entity\User;
use App\Form\SearchResourceType;
use App\Repository\ResourceRepository;
use App\Repository\SearchResourceRepository;
use App\Utils\EntityExporter;
use App\Utils\FormExporter;
use App\Utils\SearchFormPreparator;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/")
 */
class SearchController extends AbstractController
{

    /**
     * @Route("search", methods={"GET","POST"}, name="resource_search", condition="!request.isXmlHttpRequest()")
     */
    public function search(
        Request $request,
        ResourceRepository $repository,
        SearchResourceRepository $searchResourceRepository,
        SearchFormPreparator $formPreparator,
        TokenStorageInterface $tokenStorage
    ): Response
    {

        /** @var User $user */
        $searchResource = $searchResourceRepository->find(
                $tokenStorage->getToken()->getUser() instanceof UserInterface
                ?? $tokenStorage->getToken()->getUser()->getId()
            ) ?? new SearchResource();
        $form = $this->createForm(SearchResourceType::class, $searchResource);

        $form->handleRequest($request);
        $result = null;


        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() /*&& $form->isValid()*/) {

            //$em = $this->getDoctrine()->getManager();
            //$em->persist($searchResource);
            //$em->flush();

            /** @var Collection $result */
            $result = $repository->findBySearch($searchResource);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(array_map(function ($e) {
                    return (new EntityExporter())->convert($e);
                },$result));
            }

        }

        $formView = $form->createView();
        $exporter = new FormExporter($formView);

        return $this->render('resource/search.html.twig',
            [
                'json' => $formPreparator->prepare($exporter->export()),
                'form' => $formView,
                'list' => $result
            ]);
    }

    /**
     * @Route("quick-search", methods={"GET"}, name="resource_quick_search", condition="request.isXmlHttpRequest()")
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
