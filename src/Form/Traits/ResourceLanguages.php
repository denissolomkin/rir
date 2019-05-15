<?php

namespace App\Form\Traits;

use Symfony\Component\Intl\Intl;

trait ResourceLanguages
{

    protected function getLanguages(){

        $languages = array_flip(['en','uk','ru']);
        array_walk($languages, function(&$a, $b) { $a = Intl::getLanguageBundle()->getLanguageName($b); });
        return array_flip($languages);
    }
}
