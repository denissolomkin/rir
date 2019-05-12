<?php

namespace App\Utils;

use Symfony\Contracts\Translation\TranslatorInterface;

class SearchFormPreparator
{

    protected $formView;
    protected $translator;
    protected const TYPE_TEXT = 'text';
    protected const TYPE_INTEGER = 'integer';
    protected const TYPE_CHOICE = 'choice';
    protected const TYPE_ENTITY = 'entity';
    protected const TYPE_DATE = 'date';
    protected const TYPE_DATETIME = 'datetime';
    protected const TYPE_DATE_INTERVAL = 'dateinterval';
    protected const TYPE_SUBMIT = 'submit';
    protected const TYPE_HIDDEN = 'hidden';

    protected const AVAILABLE_TYPES = [
        self::TYPE_TEXT,
        self::TYPE_CHOICE,
        self::TYPE_DATETIME,
        self::TYPE_DATE,
        self::TYPE_DATE_INTERVAL,
        self::TYPE_INTEGER,
        self::TYPE_SUBMIT,
        self::TYPE_HIDDEN,
    ];

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return string
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function prepare(array $array)
    {
        $json = [];

        foreach ($array['children'] as $field => $child) {

            $child = reset($child);
            $type = $this->detectType($child);

            $json[$field] = array_filter([
                'name' => $child['full_name'],
                'type' => $type,
                'label' => $child['label'] ? $this->translator->trans($child['label']) : '',
                'multiple' => $child['multiple'] ?? false,
                'expanded' => $child['expanded'] ?? false,
                'choices' => $this->getChoices($child, $type)
            ]);

        }

        return json_encode(
            $json,
            JSON_UNESCAPED_UNICODE ^ JSON_PRETTY_PRINT
        );
    }

    protected function detectType(array $child)
    {

        $types = $child['block_prefixes'];

        foreach ($types as $type) {
            if (strpos($type, '_input') !== false) {
                return self::TYPE_ENTITY;
            }
        }

        $types = array_intersect($types, self::AVAILABLE_TYPES);

        if (empty($types)) {
            throw new \Exception(print_r($child['block_prefixes'], true));
        } else if (sizeof($types) !== 1) {
            throw new \Exception(print_r($types, true));
        } else {
            $type = reset($types);
        }

        return $type;
    }

    protected function getChoices(array $child, string $type)
    {
        $choices = [];

        switch ($type) {

            case self::TYPE_CHOICE:
                foreach ($child['choices'] as $choice) {
                    $choices[$choice['data']['id'] ?? $choice['value']] = $choice['label'];
                }
                break;

            case self::TYPE_ENTITY:
                foreach ($child[$child['name']] as $choice) {
                    $choices[$choice['id']] = $choice['name'];
                }
                break;

            case self::TYPE_HIDDEN:
                $choices[] = $child['data'];
                break;

            default:
                $choices = null;
        }

        return $choices;
    }
}