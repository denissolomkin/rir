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
use App\Entity\ResourceKeyword;
use App\Entity\SearchResource;
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

    public function findLatest(int $page = 1, ResourceKeyword $keyword = null): Pagerfanta
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('a', 't')
            ->innerJoin('p.author', 'a')
            ->leftJoin('p.keywords', 't')
            ->where('p.createdAt <= :now')
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('now', new \DateTime());

        if (null !== $keyword) {
            $qb->andWhere(':keyword MEMBER OF p.keywords')
                ->setParameter('keyword', $keyword);
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
    public function findBySearch(SearchResource $searchResource, int $limit = Resource::NUM_ITEMS): array
    {
        $query = $this->sanitizeSearchQuery($searchResource->getTitle());
        $searchTerms = $this->extractSearchTerms($query);

        $queryBuilder = $this->createQueryBuilder('p');

        if (\count($searchTerms)) {

            foreach ($searchTerms as $key => $term) {
                $queryBuilder
                    ->orWhere('p.title LIKE :t_' . $key)
                    ->setParameter('t_' . $key, '%' . $term . '%');
            }
        }

        if($author = $searchResource->getAuthor()){
            $queryBuilder
                ->andWhere('p.author = :author')
                ->setParameter('author', $author);
        }

        if($resource = $searchResource->getResourceId()){
            $queryBuilder
                ->andWhere('p.id = :resource')
                ->setParameter('resource', $resource);
        }

        if($annotation = $searchResource->getAnnotation()){
            $queryBuilder
                ->andWhere('p.annotation like %:annotation%')
                ->setParameter('annotation', $annotation);
        }

        if($extension = $searchResource->getExtension()){
            $queryBuilder
                ->andWhere('p.extension = :extension')
                ->setParameter('extension', $extension);
        }

        return $queryBuilder
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
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
                ->orWhere('p.title LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.$term.'%')
            ;
        }

        return $queryBuilder
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
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
