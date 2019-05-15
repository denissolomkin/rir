<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Resource;
use App\Entity\MetaKeyword;
use App\Entity\Search;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resource::class);
    }

    public function findLatest(int $page = 1, MetaKeyword $keyword = null): Pagerfanta
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('a', 't')
            ->innerJoin('p.author', 'a')
            ->leftJoin('p.keywords', 't')
            ->where('p.createdAt <= :now')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('now', new \DateTime())
        ;

        if (null !== $keyword) {
            $qb->andWhere(':keyword MEMBER OF p.keywords')
                ->setParameter('keyword', $keyword)
            ;
        }

        return $this->createPaginator($qb->getQuery(), $page);
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Resource::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @return Resource[]
     */
    public function findBySearch(Search $searchResource, int $limit = Resource::NUM_ITEMS): array
    {

        $queryBuilder = $this->createQueryBuilder('p');

        if ($title = $searchResource->getTitle()) {
            $query = $this->sanitizeSearchQuery($title);
            $searchTerms = $this->extractSearchTerms($query);

            if (\count($searchTerms)) {

                foreach ($searchTerms as $key => $term) {
                    $queryBuilder
                        ->orWhere('p.title LIKE :t_' . $key)
                        ->setParameter('t_' . $key, '%' . $term . '%')
                    ;
                }
            }
        }

        if (\count($authors = $searchResource->getAuthors())) {

            $ids = [];
            /** @var User $author */
            foreach ($authors as $author) {
                $ids[] = $author->getId();
            }
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('p.author', $ids));
        }

        if ($resource = $searchResource->getResourceId()) {
            $queryBuilder
                ->andWhere('p.id = :resource')
                ->setParameter('resource', $resource)
            ;
        }

        if ($annotation = $searchResource->getAnnotation()) {
            $query = $this->sanitizeSearchQuery($title);
            $searchTerms = $this->extractSearchTerms($query);

            if (\count($searchTerms)) {

                foreach ($searchTerms as $key => $term) {
                    $queryBuilder
                        ->orWhere('p.annotation LIKE :t_' . $key)
                        ->setParameter('t_' . $key, '%' . $term . '%')
                    ;
                }
            }
        }

        if ($purpose = $searchResource->getPurpose()) {
            $queryBuilder
                ->andWhere('p.purpose = :purpose')
                ->setParameter('purpose', $purpose)
            ;
        }

        if ($source = $searchResource->getSource()) {
            $queryBuilder
                ->andWhere('p.source like %:source%')
                ->setParameter('source', $source)
            ;
        }

        if ($theme = $searchResource->getTheme()) {
            $queryBuilder
                ->andWhere('p.theme like %:theme%')
                ->setParameter('theme', $theme)
            ;
        }

        if ($extension = $searchResource->getExtension()) {
            $queryBuilder
                ->andWhere('p.extension = :extension')
                ->setParameter('extension', $extension)
            ;
        }

        if ($mediaType = $searchResource->getMediaType()) {
            $queryBuilder
                ->andWhere('p.mediaType = :mediaType')
                ->setParameter('mediaType', $mediaType)
            ;
        }

        if ($documentType = $searchResource->getDocumentType()) {
            $queryBuilder
                ->andWhere('p.documentType = :documentType')
                ->setParameter('documentType', $documentType)
            ;
        }

        return $queryBuilder
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Resource[]
     */
    public function findBySearchQuery(string $rawQuery, int $limit = Resource::NUM_ITEMS): array
    {
        $query = $this->sanitizeSearchQuery($rawQuery);
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === \count($searchTerms)) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('p');

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('p.title LIKE :t_' . $key)
                ->setParameter('t_' . $key, '%' . $term . '%')
            ;
        }

        return $queryBuilder
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Removes all non-alphanumeric characters except whitespaces.
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return trim(preg_replace('/[[:space:]]+/', ' ', $query));
    }

    /**
     * Splits the search query into terms and removes the ones which are irrelevant.
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', $searchQuery));

        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
    }
}
