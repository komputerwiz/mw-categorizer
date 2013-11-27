<?php

namespace BMN\Bundle\WikiCategorizer\FrontendBundle\Form\Type;

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
        return 'bmn_wiki_categorizer_frontend_fetch';
    }
}
