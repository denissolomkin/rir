<?php

namespace App\Form;

class SearchApiByUserForm extends SearchByUserForm
{

    public function __construct()
    {
        parent::__construct(true);
    }

}
