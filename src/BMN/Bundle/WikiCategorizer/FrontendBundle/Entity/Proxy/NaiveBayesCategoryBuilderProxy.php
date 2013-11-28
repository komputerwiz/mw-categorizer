<?php

namespace BMN\Bundle\WikiCategorizer\FrontendBundle\Entity\Proxy;

use BMN\Bundle\WikiCategorizer\FrontendBundle\Entity\Category;

/**
 * A proxy class to build Category entities
 * decorated with data for a Naïve Bayes classifier.
 */
class NaiveBayesCategoryBuilderProxy
{
    /**
     * Category instance
     * @var Category
     */
    private $category;

    /**
     * Words associated with this category
     * @var array
     */
    private $words;

    /**
     * Number of documents in this category
     * @var integer
     */
    private $docCounter;

    /**
     * Total number of documents counted
     * @var integer
     */
    private static $totalDocCounter = 0;


    public function __construct($title)
    {
        $this->category = new Category();
        $this->category->setTitle($title);

        $this->words = array();
        $this->docCounter = 0;
    }


    /**
     * Set title on category (delegates to Category#setTitle())
     * @param string $title
     * @return CategoryProxy
     */
    public function setTitle($title)
    {
        $this->category->setTitle($title);
        return $this;
    }

    /**
     * Gets title on category (delegates to Category#getTitle())
     * @return string
     */
    public function getTitle()
    {
        return $this->category->getTitle();
    }

    /**
     * Increment the local and global document counters
     * @return CategoryProxy
     */
    public function incrementDocCounter()
    {
        self::$totalDocCounter++;
        $this->docCounter++;
        return $this;
    }

    /**
     * Reset the local document counter
     * @return CategoryProxy
     */
    public function resetDocCounter()
    {
        $this->docCounter = 0;
        return $this;
    }

    /**
     * Reset the global document counter
     */
    public static function resetGlobalDocCounter()
    {
        self::$totalDocCounter = 0;
    }

    /**
     * Add words to this category's bag of words (i.e. terms may appear more than once)
     * @param array $words new words to add
     */
    public function addWords(array $words)
    {
        $this->words = array_merge($this->words, $words);
        return $this;
    }

    /**
     * Finalize Naïve Bayes parameters on intertal Category instance
     * @return Category internal instance
     */
    public function getCategory()
    {
        // P(c) = log( (num articles in category) / (num articles) )
        $this->category->setProbC(log($this->docCounter / self::$totalDocCounter));

        // P(t|c) = log( (collection frequency of t in c) / (num words in c) )
        $cfs = array_count_values($this->words);
        $n = count($this->words);
        array_walk($cfs, function (&$cf) use ($n) { return $cf = log($cf/$n); });
        $this->category->setProbT($cfs);

        return $this->category;
    }
}
