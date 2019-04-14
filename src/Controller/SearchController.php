<?php

namespace App\Controller;

use App\Entity\Resource;
use App\Entity\ResourceKeyword;
use App\Entity\User;
use App\Form\ResourceType;
use App\Form\SearchType;
use App\Form\Type\DateTimePickerType;
use App\Repository\ResourceKeywordRepository;
use App\Repository\ResourceRepository;
use App\Utils\FormExporter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class SearchController extends AbstractController
{

    /**
     * @Route("search", methods={"GET"}, name="resource_search")
     */
    public function search(Request $request, ResourceRepository $repository): Response
    {
        if (!$request->isXmlHttpRequest()) {;

            $form = $this->createForm(ResourceType::class, new Resource)
                ->add('editedAt', DateTimePickerType::class, [
                    'label' => 'label.resource.edited_at',
                    'help' => 'help.resource.edited',
                ])
                ->add('createdAt', DateTimePickerType::class, [
                    'label' => 'label.resource.created_at',
                    'help' => 'help.resource.created',
                ])
                ->add('id', IntegerType::class, [
                    'attr' => ['autofocus' => true],
                    'label' => 'label.resource.title',
                ])
                ->add('author', EntityType::class, [
                    'choice_label' => 'fullname',
                    'class' => User::class,
                ])
                ->add('search', SubmitType::class);

            $formView = $form->createView();

            return $this->render('resource/search.html.twig', [
                'json' => json_encode((new FormExporter($formView))->export(), JSON_UNESCAPED_UNICODE ^ JSON_PRETTY_PRINT),
                'form' => $formView
            ]);
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
