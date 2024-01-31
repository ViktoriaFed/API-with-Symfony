<?php

namespace App\Optionsresolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ToDoOptionsResolver extends OptionsResolver
{
    public function configureName(bool $isRequired = true): self
    {
        $this->setDefined("name")->setAllowedTypes("name", "string");

        if ($isRequired) {
            $this->setRequired("name");
        }

        return $this;
    }

    // public function configureFreeCancelation(bool $isRequired = true): self
    // {
    //     $this->setDefined("FreeCancelation")->setAllowedTypes("FreeCancelation", "bool");

    //     if ($isRequired) {
    //         $this->setRequired("FreeCancelation");
    //     }

    //     return $this;
    // }
}
