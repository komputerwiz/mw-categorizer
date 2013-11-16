<?php

namespace Acme\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Acme\HelloBundle\Entity\Category;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/fetch", name="fetch")
     * @Template
     */
    public function fetchAction()
    {
        $placeholder_articles = array(
            'Jon Kleinberg',
            'Jeff Dean (computer scientist)',
        );

        return array(
            'placeholder' => $placeholder_articles[array_rand($placeholder_articles)],
        );
    }

    /**
     * @Route("/input", name="input")
     * @Template
     */
    public function inputAction(Request $request)
    {
        return array();
    }

    /**
     * @Route("/results", name="results")
     * @Template
     * @Method("POST")
     */
    public function resultsAction(Request $request)
    {

        $num_categories = rand(10, 30);

        $categories = array();
        for ($i = 0; $i < $num_categories; $i++)
            $categories[] = new Category(ucfirst($this->getLorem(rand(1,5))));

        $categories = array_unique($categories);

        foreach ($categories as $category)
            $category->setConfidence(rand(0, 100)/100);

        return array(
            'categories' => $categories,
            'article' => $request->get('article', ''),
        );
    }

    private function getLorem($num)
    {
        $words = explode(' ', 'lorem ipsum dolor sit amet consectetur adipisicing elit Quae maxime assumenda odio repellat laudantium sed est voluptas facere vel doloribus veritatis accusamus obcaecati dolor sunt id dolorem soluta cum omnis quos sint temporibus cupiditate deserunt voluptate ut eveniet itaque harum dolorum corporis unde ad alias beatae placeat facere praesentium ab inventore neque accusamus nulla nihil sequi repellendus nostrum ipsa voluptate repudiandae deserunt officiis qui ipsum eveniet dolore harum laudantium perspiciatis ab quam officia voluptatibus nisi doloribus dolorum quis consequatur quae libero dolores officia similique quia repellat deleniti perferendis error harum culpa mollitia atque voluptas animi deserunt ut minima doloremque quia');

        $ks = $num > 1 ? array_rand($words, $num) : array(array_rand($words));
        $chosen = array_map(function ($k) use ($words) { return $words[$k]; }, $ks);

        return implode(' ', $chosen);
    }
}
