<?php

namespace BMN\Bundle\WikiCategorizer\FrontendBundle\Collection;

class CategoryConfidenceMaxHeap extends \SplHeap
{
    protected function compare($a, $b)
    {
        $aConfidence = $a->getConfidence();
        $bConfidence = $b->getConfidence();

        if ($aConfidence < $bConfidence) return -1;
        elseif ($aConfidence < $bConfidence) return 1;
        else return 0;
    }
}
