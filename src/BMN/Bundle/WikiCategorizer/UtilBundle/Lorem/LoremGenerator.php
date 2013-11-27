<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\Lorem;

class LoremGenerator
{
    protected $words = array();

    public function __construct($words = null)
    {
        $this->words = $words ?: explode(' ', 'ab accusamus ad adipisicing alias amet animi assumenda atque beatae consectetur consequatur corporis culpa cupiditate deleniti deserunt dolor dolore dolorem doloremque dolores doloribus dolorum elit error est eveniet facere harum id inventore ipsa ipsum itaque laudantium libero lorem maxime minima mollitia neque nihil nisi nostrum nulla obcaecati odio officia officiis omnis perferendis perspiciatis placeat praesentium quae quam qui quia quis quos repellat repellendus repudiandae sed sequi similique sint sit soluta sunt temporibus unde ut vel veritatis voluptas voluptate voluptatibus');
    }


    public function arrayOfWords($num)
    {
        $ks = $num > 1 ? array_rand($this->words, $num) : array(array_rand($this->words));
        return array_map(function ($k) { return $this->words[$k]; }, $ks);
    }

    public function stringOfWords($num)
    {
        return implode(' ', $this->arrayOfWords($num));
    }

}
