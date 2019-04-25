<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 4/24/2019
 * Time: 11:14 PM
 */

namespace App\Handler;


class MaxDepthHandler
{

    public function __invoke($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []){
        return $innerObject->id;
    }
}