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
use Doctrine\Common\Collections\Criteria;
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

    private function createPaginator(Query $query, int $page, int $limit = Resource::NUM_ITEMS): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage($limit);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @return Resource[]
     */
    public function findBySearch(
        Search $searchResource,
        int $page = 1,
        int $limit = Resource::NUM_ITEMS
    ): Pagerfanta
    {

        $queryBuilder = $this->createQueryBuilder('p');

        /* включается политика прав доступа */
        if ($searchResource->getUser()) {

        }

        /* ищет любое вхождение в названии ресурса хотя бы по одному слову,
        например запрос по строке "on two row" вернет ресурсы с именами:
        - "hot ONtario news"
        - "simple TWO things"
        - "abuout thROWing errors"
        */

        if ($title = $searchResource->getTitle()) {
            $query = $this->sanitizeSearchQuery($title);
            $searchTerms = $this->extractSearchTerms($query);

            if (\count($searchTerms)) {

                foreach ($searchTerms as $key => $term) {
                    $expr[] = $queryBuilder->expr()->like('p.title', ':t_' . $key);

                    $queryBuilder
                        ->setParameter('t_' . $key, '%' . $term . '%')/*
                    $queryBuilder
                        ->orWhere('p.title LIKE :t_' . $key)
                        ->setParameter('t_' . $key, '%' . $term . '%')
                    */
                    ;
                }

                $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(...$expr)
                );
            }
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


        if (\count($collection = $searchResource->getKeywords())) {

            $ids = [];
            /** @var User $item */
            foreach ($collection as $item) {
                $ids[] = $item->getId();
            }
            $queryBuilder->join('p.keywords', 'k');
            $queryBuilder->andWhere($queryBuilder->expr()->in('k.id', $ids));

        }

        if (\count($collection = $searchResource->getAuthors())) {

            $ids = [];
            /** @var User $item */
            foreach ($collection as $item) {
                $ids[] = $item->getId();
            }
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('p.author', $ids));
        }

        if (\count($collection = $searchResource->getPurposes())) {

            $ids = [];
            foreach ($collection as $item) {
                $ids[] = $item->getId();
            }
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('p.purpose', $ids));
        }

        if (\count($collection = $searchResource->getExtensions())) {

            $ids = [];
            foreach ($collection as $item) {
                $ids[] = $item->getId();
            }
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('p.extension', $ids));
        }

        if (\count($collection = $searchResource->getMediaTypes())) {

            $ids = [];
            foreach ($collection as $item) {
                $ids[] = $item->getId();
            }
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('p.mediaType', $ids));
        }

        if (\count($collection = $searchResource->getDocumentTypes())) {

            $ids = [];
            foreach ($collection as $item) {
                $ids[] = $item->getId();
            }
            $queryBuilder
                ->andWhere($queryBuilder->expr()->in('p.documentType', $ids));
        }

        if ($category = $searchResource->getCategory()) {

            $queryBuilder->join('p.category', 'c');

            $queryBuilder->andWhere(
                $queryBuilder->expr()->gte(
                    'c.lft',
                    $category->getLeft()
                )
            );

            $queryBuilder->andWhere(
                $queryBuilder->expr()->lte(
                    'c.rgt',
                    $category->getRight()
                )
            );

        }

        $query = $queryBuilder
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
        ;

        if($searchResource->getUser()) {
            var_dump(
                $query->getSQL()
            );
        }
        return $this->createPaginator($query, $page, $limit);
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
