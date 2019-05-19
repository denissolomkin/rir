<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserAccess;
use App\Entity\UserGroup;
use App\Form\Type\RolesType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to edit an user.
 */
class UserByAdminForm extends UserForm
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('username', TextType::class, [
            'label' => 'label.user.username',
        ])
            ->add('roles', RolesType::class, [
                'label' => 'label.user.role',
            ])
            ->add('group', EntityType::class, [
                'class' => UserGroup::class,
                'label' => 'label.user.group',
                'choice_label' => 'name',
                'placeholder' => '',
                'required' => true,
            ])
            ->add('access', EntityType::class, [
                'class' => UserAccess::class,
                'empty_data' => 'John Doe',
                'label' => 'label.user.access',
                'choice_label' => 'name',
                'required' => true,
                'placeholder' => '',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
