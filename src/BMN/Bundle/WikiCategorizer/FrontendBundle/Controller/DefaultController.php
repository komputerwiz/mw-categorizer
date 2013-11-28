<?php

namespace BMN\Bundle\WikiCategorizer\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use BMN\Bundle\WikiCategorizer\FrontendBundle\Entity\Category;
use BMN\Bundle\WikiCategorizer\FrontendBundle\Form\Type\FetchType;
use BMN\Bundle\WikiCategorizer\FrontendBundle\Form\Type\InputType;
use BMN\Bundle\WikiCategorizer\FrontendBundle\Form\Type\VerifyType;

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
            $inputData = $this->get('mediawiki_article_fetcher')->fetch($data);
            if (!$inputData) {
                $this->get('session')->getFlashBag()->add('danger', "Could not find Wikipedia article with title <strong>{$data['title']}</strong>.");
            } else {
                $inputForm = $this->createForm(new VerifyType(), $inputData, array(
                    'action' => $this->generateUrl('results', array('verify' => true)),
                ));

                return $this->render('BMNWikiCategorizerFrontendBundle:Default:verify.html.twig', array(
                    'form' => $inputForm->createView(),
                    'article_title' => $data['title'],
                ));
            }
        }

        return $this->render('BMNWikiCategorizerFrontendBundle:Default:fetch.html.twig', array(
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
     * @Route("/results:{verify}", name="results", defaults={"verify": false})
     * @Method("POST")
     */
    public function resultsAction($verify, Request $request)
    {
        if ($verify) {
            $form = $this->createForm(new VerifyType(), null, array(
                'action' => $this->generateUrl('results', array('verify' => true)),
            ));
        } else {
            $form = $this->createForm(new InputType());
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $currentCategories = (0 === strlen(trim($data['categories']))) ? array() : explode("\n", trim($data['categories']));
            array_walk($currentCategories, function (&$c) { $c = trim($c); });
            return $this->render('BMNWikiCategorizerFrontendBundle:Default:results.html.twig', array(
                'categories' => $this->getCategories($data['content'], $currentCategories),
                'article' => $data['content'],
            ));
        }

        return $this->render('BMNWikiCategorizerFrontendBundle:Default:input.html.twig', array(
            'form' => $form->createView(),
        ));
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


    private function getCategories($content, $currentCategories)
    {
        $repo = $this->getDoctrine()->getRepository('BMNWikiCategorizerFrontendBundle:Category');
        $tokenizer = $this->get('mediawiki_tokenizer');

        $categories = $repo->findAll();
        foreach ($categories as $category) {
            $confidence = $category->getProbC();
            foreach (array_unique($tokenizer->tokenize($content)) as $term)
                $confidence += $category->getProbT($term);
            $category->setConfidence($confidence);
        }

        usort($categories, function ($a, $b) {
            return $a->getConfidence() == $b->getConfidence() ? 0 : $a->getConfidence() > $b->getConfidence() ? -1 : 1;
        });

        $top = array_slice($categories, 0, 20);

        foreach ($top as $category)
            if (in_array($category->getTitle(), $currentCategories))
                $category->setAssigned();

        return $top;
    }

    private function getDummyCategories($currentCategories)
    {
        $lorem = $this->get('lorem_generator');

        $nExisting = rand(0, ceil(count($currentCategories)/2));
        $nCategories = rand(10, 20);

        $generated = array();
        for ($i = 0; $i < $nCategories; $i++)
            $generated[] = new Category('Category:' . ucfirst($lorem->stringOfWords(rand(1,5))));

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
