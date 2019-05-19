<?php

namespace App\Controller\Api;

use App\Entity\Resource;
use App\Entity\Search;
use App\Entity\User;
use App\Form\SearchByUserForm;
use App\Repository\ResourceRepository;
use App\Repository\SearchResourceRepository;
use App\Utils\EntityExporter;
use App\Utils\FormExporter;
use App\Utils\SearchFormPreparator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Router;
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
        $search = $searchResourceRepository->find(
                $tokenStorage->getToken()->getUser() instanceof UserInterface
                ?? $tokenStorage->getToken()->getUser()->getId()
            ) ?? new Search();

        $form = $this->get('form.factory')->createNamedBuilder(
            'search',
            SearchByUserForm::class,
            $search)
            ->getForm()
        ;

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

        $search = new Search();

        $form = $this->get('form.factory')->createNamedBuilder(
            'search',
            SearchByUserForm::class,
            $search)
            ->getForm()
        ;

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                //$em = $this->getDoctrine()->getManager();
                //$em->persist($search);
                //$em->flush();
                $router = $this->get('router');
                $results = array_map(
                    function ($e) use ($router) {

                        /** @var Resource $e */
                        $data = (new EntityExporter())->convert($e);

                        if ($e->getFile()) {
                            $data['download'] = $router->generate(
                                'resource_download', [
                                'id' => $e->getId(),
                                'fileName' => $e->getFile()->getFileName()
                            ],
                                Router::ABSOLUTE_URL);
                        }

                        return $data;
                    },
                    $repository
                        ->findBySearch($search, 1, 1000)
                        ->getIterator()
                        ->getArrayCopy()
                );

            } else {
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
                return new JsonResponse(['errors' => $errors], 400);
            }

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
