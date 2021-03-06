<?php

namespace App\Form;

use App\Entity\MetaCategory;
use App\Entity\MetaKeyword;
use App\Entity\Search;
use App\Form\Traits\ResourceLanguages;
use App\Repository\MetaCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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

    private $isApi;

    public function __construct($isApi = false)
    {
        $this->isApi = $isApi;
    }

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
            ->add('theme', TextType::class, [
                'help' => 'help.resource.theme',
                'label' => 'label.resource.theme',
                'required' => false,
            ])
            ->add('purposes', EntityType::class, [
                'class' => MetaPurpose::class,
                'label' => 'label.resource.purpose',
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true,
            ])
            ->add('languages', LanguageType::class, [
                'label' => 'label.resource.language',
                'choices' => $this->getLanguages(),
                'required' => false,
                'choice_loader' => null,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('extensions', EntityType::class, [
                'class' => MetaExtension::class,
                'label' => 'label.resource.extension',
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true,
            ])
            ->add('mediaTypes', EntityType::class, [
                'class' => MetaMedia::class,
                'label' => 'label.resource.type',
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => MetaCategory::class,
                'help' => 'help.resource.category',
                'label' => 'label.resource.category',
                'choice_label' => 'name',
                'required' => false,
            ])
            ->add('keywords', EntityType::class, [
                'class' => MetaKeyword::class,
                'label' => 'label.resource.keywords',
                'required' => false,
                'multiple' => true,
            ])
            ->add('documentTypes', EntityType::class, [
                'class' => MetaDocumentType::class,
                'label' => 'label.resource.document_type',
                'help' => 'help.resource.document_type',
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true,
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
            'csrf_protection' => false,
            'data_class' => Search::class,
        ]);
    }
}
