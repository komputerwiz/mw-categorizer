<?php

namespace BMN\Bundle\WikiCategorizer\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class QueryCache
{
    const TITLE = 0;
    const CONFIDENCE = 1;

    /**
     * @ORM\Column(length=40, unique=true)
     * @ORM\Id
     */
    private $hash;

    /**
     * @ORM\Column(type="array")
     */
    private $results;


    public function __construct(array $queryTerms)
    {
        $this->hash = $this->makeHash($queryTerms);
        $this->results = array();
    }


    public function getHash()
    {
        return $this->hash;
    }

    public function setResults(array $results)
    {
        $this->results = array();
        foreach ($results as $i => $category) {
            $this->results[$i] = array(
                self::TITLE => $category->getTitle(),
                self::CONFIDENCE => $category->getConfidence()
            );
        }
        return $this;
    }

    public function getResults()
    {
        return $this->results;
    }


    private function makeHash(array $terms)
    {
        // the order of query terms should not matter.
        sort($terms);
        return sha1(serialize($terms));
    }
}
