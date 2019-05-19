<?php

namespace App\Repository;

use App\Entity\MetaAccessLevel;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class MetaAccessLevelRepository extends NestedTreeRepository
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager, $manager->getClassMetadata(MetaAccessLevel::class));
    }
}