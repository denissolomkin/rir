<?php


namespace App\Twig;

use App\Entity\Resource;
use Twig\Extension\AbstractExtension;

class ResourceConstantExtension extends AbstractExtension
{
    public function getName()
    {
        return 'resource_constant_extension';
    }

    public function getGlobals()
    {
        $class = new \ReflectionClass(Resource::class);
        $constants = $class->getConstants();

        return array(
            'Resource' => $constants
        );
    }
}
