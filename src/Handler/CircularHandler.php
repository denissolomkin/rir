<?php
/**
 * Created by PhpStorm.
 * User: Denis
 * Date: 4/24/2019
 * Time: 11:14 PM
 */

namespace App\Handler;


class CircularHandler
{

    public function __invoke($object)
    {
        return $object->id;
    }
}