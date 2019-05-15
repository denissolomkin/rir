<?php

namespace App\Form\DataTransformer;

use App\Entity\MetaKeyword;
use App\Repository\MetaKeywordRepository;
use Symfony\Component\Form\DataTransformerInterface;

class KeywordArrayToStringTransformer implements DataTransformerInterface
{
    private $repository;

    public function __construct(MetaKeywordRepository $repository)
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

        /* @var MetaKeyword[] $pieces */
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
            $object = new MetaKeyword();
            $object->setName($name);
            $pieces[] = $object;

        }

        return $pieces;
    }
}
