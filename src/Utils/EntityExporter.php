<?php
/**
 * Хелпер для конвертации данных
 */

namespace App\Utils;


use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class EntityExporter
{
    /**
     * Convert entity to array
     *
     *
     * @return array
     */
    public function convert($entity = null)
    {
        $normalizer = new GetSetMethodNormalizer(null, new CamelCaseToSnakeCaseNameConverter());

        $normalizer->setSerializer(new DoctrineEntitySerializer());

        $res = $normalizer->normalize($entity);

        if (!$res) {
            return [];
        }
        return $res;
    }
}
