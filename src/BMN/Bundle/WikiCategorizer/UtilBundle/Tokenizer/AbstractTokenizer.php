<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\Tokenizer;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractTokenizer implements TokenizerInterface
{
    protected $options;

    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    /**
     * Tokenize a string of text
     * @param  string $str input text
     * @return array       list of tokens
     */
    public abstract function tokenize($str);
}
