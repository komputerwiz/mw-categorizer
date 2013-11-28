<?php

namespace BMN\Bundle\WikiCategorizer\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BMN\Bundle\WikiCategorizer\FrontendBundle\Entity\CategoryRepository")
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="prob_c", type="float")
     */
    private $probC;

    /**
     * @var array
     *
     * @ORM\Column(name="prob_t", type="array")
     */
    private $probT;

    // unmapped variables
    private $assigned;
    private $confidence;


    public function __construct($title = null)
    {
        $this->title = $title;
        $this->probT = array();
    }

    public function __toString()
    {
        return $this->getTitle();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Category
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set P(c)
     *
     * @param float $probC
     * @return Category
     */
    public function setProbC($probC)
    {
        $this->probC = $probC;
        return $this;
    }

    /**
     * Get P(c)
     *
     * @return float
     */
    public function getProbC()
    {
        return $this->probC;
    }

    /**
     * Set P(t|c) in array form: array(t1 => P(t1|c), t2 => P(t2|c), ...)
     *
     * @param array $probT
     * @return Category
     */
    public function setProbT($probT)
    {
        $this->probT = $probT;
        return $this;
    }

    /**
     * Get P(t|c)
     *
     * @return array
     */
    public function getProbT($term = null)
    {
        // ignore unknown terms
        if (null === $term)
            return $this->probT;
        else
            return isset($this->probT[$term]) ? $this->probT[$term] : 1;
    }

    public function setConfidence($confidence)
    {
        $this->confidence = $confidence;
        return $this;
    }

    public function getConfidence()
    {
        return $this->confidence;
    }

    public function setAssigned($assigned = true)
    {
        $this->assigned = $assigned;
        return $this;
    }

    public function isAssigned()
    {
        return $this->assigned;
    }
}
