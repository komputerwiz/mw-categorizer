<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\Tokenizer;

interface TokenizerInterface
{
    /**
     * Tokenize a string of text
     * @param  string $str input text
     * @return array       list of tokens
     */
    public function tokenize($str);
}
