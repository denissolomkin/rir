<?php

namespace App\Form;

use App\Entity\Search;
use App\Form\Traits\ResourceLanguages;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\User;
use App\Entity\MetaDocumentType;
use App\Entity\MetaExtension;
use App\Entity\MetaMedia;
use App\Entity\MetaPurpose;
use App\Form\Type\KeywordsInputType;

class SearchByUserForm extends AbstractType
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
                'required' => false,
            ])
            ->add('source', TextType::class, [
                'help' => 'help.resource.source',
                'label' => 'label.resource.source',
                'required' => false,
            ])
            ->add('languages', LanguageType::class, [
                'label' => 'label.resource.language',
                'choices' => $this->getLanguages(),
                'required' => false,
                'choice_loader' => null,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('theme', TextType::class, [
                'help' => 'help.resource.theme',
                'label' => 'label.resource.theme',
                'required' => false,
            ])
            ->add('extension', EntityType::class, [
                'class' => MetaExtension::class,
                'label' => 'label.resource.extension',
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('mediaType', EntityType::class, [
                'class' => MetaMedia::class,
                'label' => 'label.resource.type',
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('category', TextType::class, [
                'help' => 'help.resource.category',
                'label' => 'label.resource.category',
                'required' => false,
            ])
            ->add('keywords', KeywordsInputType::class, [
                'label' => 'label.resource.keywords',
                'required' => false,
            ])
            ->add('purpose', EntityType::class, [
                'class' => MetaPurpose::class,
                'label' => 'label.resource.purpose',
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('documentType', EntityType::class, [
                'class' => MetaDocumentType::class,
                'label' => 'label.resource.document_type',
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('authors', EntityType::class, [
                'choice_label' => 'fullname',
                'class' => User::class,
                'label' => 'label.resource.author',
                'required' => false,
                'multiple' => true,
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
            'data_class' => Search::class,
        ]);
    }
}
