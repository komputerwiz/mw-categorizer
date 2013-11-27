<?php

namespace BMN\Bundle\WikiCategorizer\FrontendBundle\Entity;

class Category
{
    private $confidence;
    private $title;
    private $assigned;


    public function __construct($title, $assigned = false)
    {
        $this->title = $title;
        $this->assigned = $assigned;
    }

    public function __toString()
    {
        return $this->getTitle();
    }


    public function getTitle()
    {
        return $this->title;
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
