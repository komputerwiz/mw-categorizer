<?php

namespace Acme\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Acme\HelloBundle\Entity\Category;
use Acme\HelloBundle\Form\Type\FetchType;
use Acme\HelloBundle\Form\Type\InputType;

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
     * @Method("GET")
     */
    public function fetchAction()
    {
        $form = $this->createForm(new FetchType(), null, array(
            'action' => $this->generateUrl('verify'),
        ));

        return array(
            'placeholder' => $this->getRandomPlaceholder(),
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/fetch/verify", name="verify")
     * @Method("POST")
     */
    public function verifyAction(Request $request)
    {
        $fetchForm = $this->createForm(new FetchType());
        $fetchForm->handleRequest($request);

        if ($fetchForm->isValid()) {
            $data = $fetchForm->getData();
            $title = $data['title'];
            $inputData = $this->getWikipediaArticle($title);
            if (!$inputData) {
                $this->get('session')->getFlashBag()->add('danger', "Could not find Wikipedia article with title <strong>$title</strong>.");
            } else {
                $inputForm = $this->createForm(new InputType(), $inputData, array(
                    'action' => $this->generateUrl('results'),
                ));

                return $this->render('AcmeHelloBundle:Default:verify.html.twig', array(
                    'form' => $inputForm->createView(),
                    'article_title' => $title,
                ));
            }
        }

        return $this->render('AcmeHelloBundle:Default:fetch.html.twig', array(
            'form' => $fetchForm->createView(),
            'placeholder' => $this->getRandomPlaceholder(),
        ));
    }

    /**
     * @Route("/input", name="input")
     * @Template
     * @Method("GET")
     */
    public function inputAction(Request $request)
    {
        $form = $this->createForm(new InputType(), null, array(
            'action' => $this->generateUrl('results'),
        ));

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/results", name="results")
     * @Method("POST")
     */
    public function resultsAction(Request $request)
    {
        $form = $this->createForm(new InputType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            return $this->render('AcmeHelloBundle:Default:results.html.twig', array(
                'categories' => $this->getCategories($data['content'], explode("\n", trim($data['categories']))),
                'article' => $data['content'],
            ));
        }

        return $this->render('AcmeHelloBundle:Default:input.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    private function getLorem($num)
    {
        $words = explode(' ', 'ab accusamus ad adipisicing alias amet animi assumenda atque beatae consectetur consequatur corporis culpa cupiditate deleniti deserunt dolor dolore dolorem doloremque dolores doloribus dolorum elit error est eveniet facere harum id inventore ipsa ipsum itaque laudantium libero lorem maxime minima mollitia neque nihil nisi nostrum nulla obcaecati odio officia officiis omnis perferendis perspiciatis placeat praesentium quae quam qui quia quis quos repellat repellendus repudiandae sed sequi similique sint sit soluta sunt temporibus unde ut vel veritatis voluptas voluptate voluptatibus');

        $ks = $num > 1 ? array_rand($words, $num) : array(array_rand($words));
        $chosen = array_map(function ($k) use ($words) { return $words[$k]; }, $ks);

        return implode(' ', $chosen);
    }

    private function getRandomPlaceholder()
    {
        $titles = array(
            'Albert Einstein',
            'Jon Kleinberg',
            'Jeff Dean (computer scientist)',
        );
        return $titles[array_rand($titles)];
    }

    private function getWikipediaArticle($title)
    {
        // send API request
        $url = 'http://en.wikipedia.org/w/api.php?format=json&action=query&prop=revisions|categories&rvprop=content&cllimit=500&titles=%s';
        $ch = curl_init(sprintf($url, urlencode($title)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "WikipediaCategorizer/0.1 (mbarry@tamu.edu)");
        $json = curl_exec($ch);

        $wikiData = json_decode($json, true);

        foreach ($wikiData['query']['pages'] as $id => $page) {
            // there should only be one $page
            if (array_key_exists('missing', $page)) return false;
            $categories = '';
            if (array_key_exists('categories', $page))
                foreach ($page['categories'] as $category)
                    $categories .= $category['title'] . "\n";

            $categories = trim($categories);

            $content = $page['revisions'][0]['*'];
            return compact('content', 'categories');
        }
    }

    private function getCategories($content, $currentCategories)
    {
        $nExisting = rand(0, floor(count($currentCategories)/2));
        $nCategories = rand(10, 20);

        $generated = array();
        for ($i = 0; $i < $nCategories; $i++)
            $generated[] = new Category('Category:' . ucfirst($this->getLorem(rand(1,5))));

        $generated = array_unique($generated);

        shuffle($currentCategories);
        $existing = array();
        for ($i = 0; $i < $nExisting; $i++)
            $existing[] = new Category($currentCategories[$i], true);

        $categories = array_merge($generated, $existing);

        foreach ($categories as $category)
            $category->setConfidence(rand(0, 100)/100);

        return $categories;
    }
}
