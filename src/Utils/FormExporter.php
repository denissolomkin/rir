<?php

namespace App\Utils;

use Symfony\Component\Form\FormView;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FormExporter
{

    protected $formView;

    public function __construct(FormView $formView)
    {
        $this->formView = $formView;
    }

    /**
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function export()
    {

        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, []);

        $data = array();
        $data['vars'] = $serializer->normalize($this->formView->vars);

        foreach ($this->formView->children as $key => $childView) {
            $data['children'][$key] = (new self($childView))->export();
        }

        return $serializer->normalize($data);
    }
}
