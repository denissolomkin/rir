<?php

namespace App\Form;

use App\Entity\Resource;
use App\Entity\File;
use App\Form\Traits\ResourceLanguages;
use App\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\MetaAccessLevel;
use App\Entity\MetaDocumentType;
use App\Entity\MetaExtension;
use App\Entity\MetaMedia;
use App\Entity\MetaPurpose;
use App\Form\Type\KeywordsInputType;

class ResourceForm extends AbstractType
{
    use ResourceLanguages;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('title', null, [
                'attr' => ['autofocus' => true],
                'label' => 'label.resource.title',
                'required' => true,
            ])
            ->add('annotation', TextareaType::class, [
                'help' => 'help.resource.annotation',
                'label' => 'label.resource.annotation',
                'required' => true,
            ])
            ->add('source', TextType::class, [
                'help' => 'help.resource.source',
                'label' => 'label.resource.source',
                'required' => true,
            ])
            ->add('theme', TextType::class, [
                'help' => 'help.resource.theme',
                'label' => 'label.resource.theme',
                'required' => true,
            ])
            ->add('extension', EntityType::class, [
                'class' => MetaExtension::class,
                'label' => 'label.resource.extension',
                'help' => 'help.resource_extension',
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('mediaType', EntityType::class, [
                'class' => MetaMedia::class,
                'label' => 'label.resource.type',
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('size', IntegerType::class, [
                'help' => 'help.resource_size',
                'label' => 'label.resource.size',
                'required' => true,
            ])
            ->add('category', TextType::class, [
                'help' => 'help.resource_category',
                'label' => 'label.resource.category',
                'required' => true,
            ])
            ->add('keywords', KeywordsInputType::class, [
                'label' => 'label.resource.keywords',
                'required' => true,
            ])
            ->add('accessLevel', EntityType::class, [
                'class' => MetaAccessLevel::class,
                'label' => 'label.resource.access_level',
                'choice_label' => 'name',
                'required' => true,
                'expanded' => true,
            ])
            ->add('purpose', EntityType::class, [
                'class' => MetaPurpose::class,
                'label' => 'label.resource.purpose',
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('documentType', EntityType::class, [
                'class' => MetaDocumentType::class,
                'label' => 'label.resource.document_type',
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('expiredAt', DateTimePickerType::class, [
                'label' => 'label.resource.expired_at',
                'help' => 'help.resource.expired',
                'required' => true,
            ])
            ->add('language', LanguageType::class, [
                'help' => 'help.resource_language',
                'label' => 'label.resource.language',
                'choices' => $this->getLanguages(),
                'required' => true,
                'choice_loader' => null,
            ])
            ->add('upload', EntityType::class, [
                'class' => File::class,
                'choice_label' => 'fileName',
                'required' => false,
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
