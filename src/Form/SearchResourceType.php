<?php

namespace App\Form;

use App\Entity\Resource;
use App\Entity\SearchResource;
use App\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;

class SearchResourceType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        self::build($builder, $options, false);
        $builder
            ->add('statuses', ChoiceType::class, [
                'choices' => array_flip(Resource::STATUSES),
                'label' => 'label.resource.statuses',
                'required' => false,
                'multiple' => true,
            ])
            ->add('editedAt', DateTimePickerType::class, [
                'label' => 'label.resource.edited_at',
                'help' => 'help.resource.edited',
            ])
            ->add('createdAt', DateType::class, [
                'label' => 'label.resource.created_at',
                'help' => 'help.resource.created',
                'required' => false,
            ])
            ->add('publishedAt', DateIntervalType::class, [
                'label' => 'label.resource.published_at',
                'help' => 'help.resource.published',
                'required' => false,
            ])
            ->add('resourceId', IntegerType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.resource.id',
                'required' => false,
            ])
            ->add('author', EntityType::class, [
                'choice_label' => 'fullname',
                'class' => User::class,
                'label' => 'label.resource.author',
                'required' => false,
            ])
            ->add('search', SubmitType::class, [
                'label' => 'action.search',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchResource::class,
        ]);
    }
}
