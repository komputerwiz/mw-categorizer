<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\Tokenizer;

class MediaWikiTokenizer implements TokenizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function tokenize($str)
    {
        preg_match_all('([a-z]+)', strtolower($str), $words);
        return $words[0];
    }
}
