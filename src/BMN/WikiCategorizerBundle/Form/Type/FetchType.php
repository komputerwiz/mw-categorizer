<?php

namespace Acme\HelloBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FetchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array(
            'constraints' => new Assert\NotBlank(),
        ));
        $builder->add('fetch', 'submit');
    }

    public function getName()
    {
        return 'acme_fetch';
    }
}
