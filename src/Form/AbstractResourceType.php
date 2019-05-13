<?php

namespace App\Form;

use App\Entity\ResourceAccessLevel;
use App\Entity\ResourceDocumentType;
use App\Entity\ResourceExtension;
use App\Entity\ResourceMediaType;
use App\Entity\ResourcePurpose;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\KeywordsInputType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;

abstract class AbstractResourceType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function build(FormBuilderInterface $builder, array $options, $required = true): void
    {

        $builder
            ->add('title', null, [
                'attr' => ['autofocus' => true],
                'label' => 'label.resource.title',
                'required' => $required,
            ])
            ->add('annotation', TextareaType::class, [
                'help' => 'help.resource.annotation',
                'label' => 'label.resource.annotation',
                'required' => $required,
            ])
            ->add('source', TextType::class, [
                'help' => 'help.resource.source',
                'label' => 'label.resource.source',
                'required' => $required,
            ])
            ->add('theme', TextType::class, [
                'help' => 'help.resource.theme',
                'label' => 'label.resource.theme',
                'required' => $required,
            ])
            ->add('extension', EntityType::class, [
                'class' => ResourceExtension::class,
                'label' => 'label.resource.extension',
                'help' => 'help.resource_extension',
                'choice_label' => 'name',
                'required' => $required,
            ])
            ->add('mediaType', EntityType::class, [
                'class' => ResourceMediaType::class,
                'label' => 'label.resource.type',
                'choice_label' => 'name',
                'required' => $required,
            ])
            ->add('size', IntegerType::class, [
                'help' => 'help.resource_size',
                'label' => 'label.resource.size',
                'required' => $required,
            ])
            ->add('category', TextType::class, [
                'help' => 'help.resource_category',
                'label' => 'label.resource.category',
                'required' => $required,
            ])
            ->add('expiredAt', DateTimePickerType::class, [
                'label' => 'label.resource.expired_at',
                'help' => 'help.resource.expired',
                'required' => $required,
            ])
            ->add('keywords', KeywordsInputType::class, [
                'label' => 'label.resource.keywords',
                'required' => $required,
            ])
            ->add('accessLevel', EntityType::class, [
                'class' => ResourceAccessLevel::class,
                'label' => 'label.resource.access_level',
                'choice_label' => 'name',
                'required' => $required,
                'expanded' => true,
            ])
            ->add('purpose', EntityType::class, [
                'class' => ResourcePurpose::class,
                'label' => 'label.resource.purpose',
                'choice_label' => 'name',
                'required' => $required,
            ])
            ->add('documentType', EntityType::class, [
                'class' => ResourceDocumentType::class,
                'label' => 'label.resource.document_type',
                'choice_label' => 'name',
                'required' => $required,
            ])
        ;
    }

    protected function getLanguages(){

        $languages = array_flip(['en','uk','ru']);
        array_walk($languages, function(&$a, $b) { $a = Intl::getLanguageBundle()->getLanguageName($b); });
        return array_flip($languages);
    }
}
