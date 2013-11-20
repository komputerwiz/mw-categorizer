<?php

namespace Acme\HelloBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class InputType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array(
                'constraints' => new Assert\NotBlank(),
            ))
            ->add('categories', 'textarea')
            ->add('submit_bottom', 'submit')
        ;
    }

    public function getName()
    {
        return 'acme_input';
    }
}
