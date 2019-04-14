<?php

namespace App\Form;

use App\Entity\Resource;
use App\Entity\ResourceAccessLevel;
use App\Entity\ResourceDocumentType;
use App\Entity\ResourcePurpose;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\KeywordsInputType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'attr' => ['autofocus' => true],
                'label' => 'label.resource.title',
            ])
            ->add('annotation', TextareaType::class, [
                'help' => 'help.resource_annotation',
                'label' => 'label.resource.annotation',
            ])
            ->add('source', TextType::class, [
                'help' => 'help.resource_source',
                'label' => 'label.resource.source',
            ])
            ->add('theme', TextType::class, [
                'help' => 'help.resource_theme',
                'label' => 'label.resource.theme',
            ])
            ->add('extension', TextType::class, [
                'help' => 'help.resource_extension',
                'label' => 'label.resource.extension',
            ])
            ->add('size', IntegerType::class, [
                'help' => 'help.resource_size',
                'label' => 'label.resource.size',
            ])
            ->add('language', TextType::class, [
                'help' => 'help.resource_language',
                'label' => 'label.resource.language',
            ])
            ->add('category', TextType::class, [
                'help' => 'help.resource_category',
                'label' => 'label.resource.category',
            ])
            ->add('expiredAt', DateTimePickerType::class, [
                'label' => 'label.resource.expired_at',
                'help' => 'help.resource.expired',
            ])
            ->add('keywords', KeywordsInputType::class, [
                'label' => 'label.resource.keywords',
                'required' => false,
            ])
            ->add('accessLevel', EntityType::class, [
                'class' => ResourceAccessLevel::class,
                'label' => 'label.resource.access_level',
                'choice_label' => 'name',
                'required' => true,
                'expanded' => true,
            ])
            ->add('purpose', EntityType::class, [
                'class' => ResourcePurpose::class,
                'label' => 'label.resource.purpose',
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('documentType', EntityType::class, [
                'class' => ResourceDocumentType::class,
                'label' => 'label.resource.document_type',
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('type', EntityType::class, [
                'class' => \App\Entity\ResourceType::class,
                'label' => 'label.resource.type',
                'choice_label' => 'name',
                'required' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resource::class,
        ]);
    }
}
