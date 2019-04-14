<?php

namespace App\Form\DataTransformer;

use App\Entity\ResourceKeyword;
use App\Repository\ResourceKeywordRepository;
use Symfony\Component\Form\DataTransformerInterface;

class KeywordArrayToStringTransformer implements DataTransformerInterface
{
    private $repository;

    public function __construct(ResourceKeywordRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($pieces): string
    {
        // The value received is an array of ResourceKeyword objects generated with
        // Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer::transform()
        // The value returned is a string that concatenates the string representation of those objects

        /* @var ResourceKeyword[] $pieces */
        return implode(',', $pieces);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string): array
    {
        if ('' === $string || null === $string) {
            return [];
        }

        $names = array_filter(array_unique(array_map('trim', explode(',', $string))));

        // Get the current pieces and find the new ones that should be created.
        $pieces = $this->repository->findBy([
            'name' => $names,
        ]);
        $newNames = array_diff($names, $pieces);
        foreach ($newNames as $name) {
            $object = new ResourceKeyword();
            $object->setName($name);
            $pieces[] = $object;

        }

        return $pieces;
    }
}
