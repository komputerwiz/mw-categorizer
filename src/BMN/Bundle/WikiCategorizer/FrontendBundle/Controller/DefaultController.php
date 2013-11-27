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
            $title = $data['title'];

            $inputData = $this->get('mediawiki_article_fetcher')->fetch($data);
            if (!$inputData) {
                $this->get('session')->getFlashBag()->add('danger', "Could not find Wikipedia article with title <strong>$title</strong>.");
            } else {
                $inputForm = $this->createForm(new VerifyType(), $inputData, array(
                    'action' => $this->generateUrl('results'),
                ));

                return $this->render('BMNWikiCategorizerFrontendBundle:Default:verify.html.twig', array(
                    'form' => $inputForm->createView(),
                    'article_title' => $title,
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
     * @Route("/results", name="results")
     * @Method("POST")
     */
    public function resultsAction(Request $request)
    {
        $form = $this->createForm(new InputType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            return $this->render('BMNWikiCategorizerFrontendBundle:Default:results.html.twig', array(
                'categories' => $this->getRandomCategories($data['content'], explode("\n", trim($data['categories']))),
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


    private function getRandomCategories($content, $currentCategories)
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
