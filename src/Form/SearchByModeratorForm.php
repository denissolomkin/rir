<?php

namespace App\Form;

use App\Entity\Resource;
use App\Entity\Search;
use App\Form\Traits\ResourceLanguages;
use App\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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

class SearchByModeratorForm extends AbstractType
{
    use ResourceLanguages;
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statuses', ChoiceType::class, [
                'choices' => array_flip(Resource::STATUSES),
                'label' => 'label.resource.statuses',
                'required' => false,
                'multiple' => true,
            ])
            ->add('languages', LanguageType::class, [
                'help' => 'help.resource_language',
                'label' => 'label.resource.language',
                'choices' => $this->getLanguages(),
                'required' => false,
                'choice_loader' => null,
                'multiple' => true,
            ])
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
            ->add('theme', TextType::class, [
                'help' => 'help.resource.theme',
                'label' => 'label.resource.theme',
                'required' => false,
            ])
            ->add('extension', EntityType::class, [
                'class' => MetaExtension::class,
                'label' => 'label.resource.extension',
                'help' => 'help.resource_extension',
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
                'help' => 'help.resource_category',
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
            ->add('resourceId', IntegerType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'label.resource.id',
                'required' => false,
            ])
            ->add('authors', EntityType::class, [
                'choice_label' => 'fullname',
                'class' => User::class,
                'label' => 'label.resource.author',
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
