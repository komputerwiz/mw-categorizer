<?php

namespace Acme\HelloBundle\Entity;

class Category
{
    private $confidence;
    private $title;


    public function __construct($title)
    {
        $this->title = $title;
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
}
