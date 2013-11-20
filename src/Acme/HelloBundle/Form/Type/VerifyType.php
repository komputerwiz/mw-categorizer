<?php

namespace Acme\HelloBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class VerifyType extends InputType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('submit_top', 'submit');
        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'acme_verify';
    }
}
