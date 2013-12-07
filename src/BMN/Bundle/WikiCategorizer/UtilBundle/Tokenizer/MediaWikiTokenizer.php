<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\Tokenizer;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaWikiTokenizer extends AbstractTokenizer
{
    /**
     * Words commonly used in the English language (very commonly)
     * @see http://norm.al/2009/04/14/list-of-english-stop-words/
     * @var array
     */
    private static $stopWords = array(
        "a", "about", "above", "across", "after", "afterwards", "again", "against", "all", "almost",
        "alone", "along", "already", "also", "although", "always", "am", "among", "amongst",
        "amoungst", "amount", "an", "and", "another", "any", "anyhow", "anyone", "anything",
        "anyway", "anywhere", "are", "around", "as", "at", "back", "be", "became", "because",
        "become", "becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below",
        "beside", "besides", "between", "beyond", "bill", "both", "bottom", "but", "by", "call",
        "can", "cannot", "cant", "co", "computer", "con", "could", "couldnt", "cry", "de",
        "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight",
        "either", "eleven", "else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every",
        "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find",
        "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from",
        "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he",
        "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself",
        "him", "himself", "his", "how", "however", "hundred", "i", "ie", "if", "in", "inc",
        "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter",
        "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might",
        "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my",
        "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no",
        "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often",
        "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours",
        "ourselves", "out", "over", "own", "part", "per", "perhaps", "please", "put", "rather",
        "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she",
        "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow",
        "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system",
        "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence",
        "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they",
        "thick", "thin", "third", "this", "those", "though", "three", "through", "throughout",
        "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty",
        "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well",
        "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas",
        "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither",
        "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without",
        "would", "yet", "you", "your", "yours", "yourself", "yourselves"
    );

    private static $porterStopWords = array(
        'a', 'about', 'abov', 'across', 'after', 'afterward', 'again', 'against', 'all', 'almost',
        'alon', 'along', 'alreadi', 'also', 'although', 'alwai', 'am', 'among', 'amongst',
        'amoungst', 'amount', 'an', 'and', 'anoth', 'ani', 'anyhow', 'anyon', 'anyth', 'anywai',
        'anywher', 'ar', 'around', 'as', 'at', 'back', 'be', 'becam', 'becaus', 'becom', 'been',
        'befor', 'beforehand', 'behind', 'below', 'besid', 'between', 'beyond', 'bill', 'both',
        'bottom', 'but', 'by', 'call', 'can', 'cannot', 'cant', 'co', 'comput', 'con', 'could',
        'couldnt', 'cry', 'de', 'describ', 'detail', 'do', 'done', 'down', 'due', 'dure', 'each',
        'eg', 'eight', 'either', 'eleven', 'els', 'elsewher', 'empti', 'enough', 'etc', 'even',
        'ever', 'everi', 'everyon', 'everyth', 'everywher', 'except', 'few', 'fifteen', 'fifi',
        'fill', 'find', 'fire', 'first', 'five', 'for', 'former', 'formerli', 'forti', 'found',
        'four', 'from', 'front', 'full', 'further', 'get', 'give', 'go', 'had', 'ha', 'hasnt',
        'have', 'he', 'henc', 'her', 'here', 'hereaft', 'herebi', 'herein', 'hereupon', 'herself',
        'him', 'himself', 'hi', 'how', 'howev', 'hundr', 'i', 'ie', 'if', 'in', 'inc', 'inde',
        'interest', 'into', 'is', 'it', 'itself', 'keep', 'last', 'latter', 'latterli', 'least',
        'less', 'ltd', 'made', 'mani', 'mai', 'me', 'meanwhil', 'might', 'mill', 'mine', 'more',
        'moreov', 'most', 'mostli', 'move', 'much', 'must', 'my', 'myself', 'name', 'neither',
        'never', 'nevertheless', 'next', 'nine', 'no', 'nobodi', 'none', 'noon', 'nor', 'not',
        'noth', 'now', 'nowher', 'of', 'off', 'often', 'on', 'onc', 'onli', 'onto', 'or', 'other',
        'otherwis', 'our', 'ourselv', 'out', 'over', 'own', 'part', 'per', 'perhap', 'pleas', 'put',
        'rather', 're', 'same', 'see', 'seem', 'seriou', 'sever', 'she', 'should', 'show', 'side',
        'sinc', 'sincer', 'six', 'sixti', 'so', 'some', 'somehow', 'someon', 'someth', 'sometim',
        'somewher', 'still', 'such', 'system', 'take', 'ten', 'than', 'that', 'the', 'their',
        'them', 'themselv', 'then', 'thenc', 'there', 'thereaft', 'therebi', 'therefor', 'therein',
        'thereupon', 'these', 'thei', 'thick', 'thin', 'third', 'thi', 'those', 'though', 'three',
        'through', 'throughout', 'thru', 'thu', 'to', 'togeth', 'too', 'top', 'toward', 'twelv',
        'twenti', 'two', 'un', 'under', 'until', 'up', 'upon', 'us', 'veri', 'via', 'wa', 'we',
        'well', 'were', 'what', 'whatev', 'when', 'whenc', 'whenev', 'where', 'whereaft', 'wherea',
        'wherebi', 'wherein', 'whereupon', 'wherev', 'whether', 'which', 'while', 'whither', 'who',
        'whoever', 'whole', 'whom', 'whose', 'why', 'will', 'with', 'within', 'without', 'would',
        'yet', 'you', 'your', 'yourself', 'yourselv'
    );

    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'strip_tags' => true,
            'strip_apostrophes' => true,
            'remove_stop_words' => true,
            'stemming' => true,
            'parse_wiki_text' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function tokenize($str)
    {
        if ($this->options['parse_wiki_text']){
            // wikiParser can't handle templates, so we'll get rid of them here
            $str = $this->removeTemplates($str);
            $parser = new \wikiParser();
            $str = $parser->parse($str);
        }

        if ($this->options['strip_tags'])
            $str = strip_tags($str);

        if ($this->options['strip_apostrophes'])
            $str = str_replace("'", '', $str);

        // look for groupings of alpha characters; disregard symbols and numbers
        preg_match_all('([a-z]+)', strtolower($str), $words);
        $tokens = $words[0];

        // if ($this->options['remove_stop_words'])
        //     $tokens = array_filter($tokens, function ($w) {
        //         return !in_array($w, static::$stopWords);
        //     });

        if ($this->options['stemming'])
            $tokens = array_map(function ($t) {
                return \Porter::Stem($t);
            }, $tokens);

        if ($this->options['remove_stop_words'])
            $tokens = array_filter($tokens, function ($w) {
                return !in_array($w, static::$porterStopWords);
            });

        return $tokens;
    }


    private function removeTemplates($str)
    {
        $nestLevel = 0;
        $output = array();
        $buffer = array();
        $state = 0;

        foreach (str_split($str) as $char) {
            switch ($state) {
                case 0:
                    // STATE 0: default iterative state
                    if ('{' === $char) {
                        // check for following open brace
                        // store current character in buffer in case next char is not a brace
                        $buffer[] = $char;
                        $state = 1;
                    } elseif('}' === $char && $nestLevel > 0) {
                        // check for following close brace
                        $state = 2;
                    } elseif(0 === $nestLevel) {
                        // write to output only if we are not within a template
                        $output[] = $char;
                    }
                    break;

                case 1:
                    // STATE 1: seen open brace
                    if ($char == '{') {
                        // two opening braces in a row: increase nest level
                        $nestLevel++;
                    } else {
                        // next char wasn't another opening brace; flush buffer to output
                        $buffer[] = $char;
                        $output[] = implode('', $buffer);
                    }

                    // clear buffer
                    $buffer = array();

                    // go back to default state
                    $state = 0;
                    break;

                case 2:
                    // STATE 2: ignoring text, found a close brace while we were inside a template
                    if ($char == '}') $nestLevel--;
                    $state = 0;
                    break;
            }
        }
        return implode('', $output);
    }
}
