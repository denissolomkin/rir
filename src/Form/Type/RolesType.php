<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * @author Echarbeto
 */
class RolesType extends AbstractType
{

    private $roles = [];

    /**
     * RolesType constructor.
     * @param RoleHierarchyInterface|array $rolehierarchy
     */
    public function __construct(RoleHierarchyInterface $rolehierarchy)
    {
        $roles = array();

        array_walk_recursive($rolehierarchy, function ($item) use (&$roles) {
            $roles[$item] = $item;
        });
        ksort($roles);
        $this->roles = array_unique($roles);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->roles,
            'attr' => array(
                'class' => 'form-control',
                'aria-hidden' => 'true',
                'ref' => 'input',
                'multiple' => '',
                'tabindex' => '-1'
            ),
            'required' => true,
            'multiple' => true,
            'empty_data' => null,
            'label_attr' => array(
                'class' => 'control-label'
            )
        ));
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

}