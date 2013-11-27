<?php

namespace BMN\Bundle\WikiCategorizer\UtilBundle\Twig\Extension;

class ArrayExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('sort_by', array($this, 'sortByFilter')),
        );
    }

    public function sortByFilter($arr, $attr = null, $options = array('caseSensitive' => false, 'reverse' => false))
    {
        if (null === $attr) return $arr;

        $sortItems = array();
        $excluded = array();

        foreach ($arr as $k => $v) {
            if ($v instanceof ArrayAccess && isset($v[$attr])) {
                $v = $v[$attr];
            } elseif (is_object($v)) {
                $getter = preg_replace("/[^a-zA-Z0-9]/", "", "get".$attr);
                $isser  = preg_replace("/[^a-zA-Z0-9]/", "", "is".$attr);

                if (method_exists($v, $getter)) {
                    $v = $v->$getter();
                } elseif (method_exists($v, $isser)) {
                    $v = $v->$isser();
                } else {
                    array_push($excluded, $v);
                    continue;
                }
            } elseif (is_array($v) && isset($v[$attr])) {
                $v = $v[$attr];
            } else {
                array_push($excluded, $v);
                continue;
            }

            // applies "caseSensitive" option
            if (!isset($options['caseSensitive']) || true !== $options['caseSensitive']) {
                if (is_string($v)) {
                    $v = strtolower($v);
                }
            }

            $sortItems[$k] = $v;
        }


        if (isset($options['reverse']) && true === $options['reverse']) {
            arsort($sortItems);
        } else {
            asort($sortItems);
        }

        foreach ($sortItems as $k => $v) $sortItems[$k] = $arr[$k];

        return array_merge($sortItems, $excluded);
    }

    public function getName()
    {
        return 'bmn_wiki_categorizer_util_twig_extension_array';
    }
}
