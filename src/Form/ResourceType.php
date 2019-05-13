<?php

namespace App\Form;

use App\Entity\Resource;
use App\Entity\ResourceFile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResourceType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        self::build($builder, $options, true);

        $builder
            ->add('language', LanguageType::class, [
                'help' => 'help.resource_language',
                'label' => 'label.resource.language',
                'choices' => $this->getLanguages(),
                'required' => true,
                'choice_loader' => null,
            ])
            ->add('upload', EntityType::class, [
                'class' => ResourceFile::class,
                'choice_label' => 'fileName',
                'required' => false,
            ]);
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
