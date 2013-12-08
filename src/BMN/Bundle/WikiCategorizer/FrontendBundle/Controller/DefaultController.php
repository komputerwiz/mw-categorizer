<?php

namespace BMN\Bundle\WikiCategorizer\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use BMN\Bundle\WikiCategorizer\FrontendBundle\Collection\CategoryConfidenceMaxHeap;
use BMN\Bundle\WikiCategorizer\FrontendBundle\Entity\Category;
use BMN\Bundle\WikiCategorizer\FrontendBundle\Entity\QueryCache;
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

            $categories = $this->getCategories($data['content']);
            foreach ($categories as $category)
                if (in_array($category->getTitle(), $currentCategories))
                    $category->setAssigned();

            return $this->render('BMNWikiCategorizerFrontendBundle:Default:results.html.twig', array(
                'categories' => $categories,
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


    private function getCategories($content)
    {
        // tokenize query
        $tokenizer = $this->get('mediawiki_tokenizer');
        $terms = array_unique($tokenizer->tokenize($content));

        // attempt to fetch cached results
        $cacheRepo = $this->getDoctrine()->getRepository('BMNWikiCategorizerFrontendBundle:QueryCache');
        $cache = new QueryCache($terms);
        $realCache = $cacheRepo->find($cache->getHash());

        if ($realCache) {
            // hydrate array of Category objects; order is preserved by serialization
            return array_map(function ($info) {
                $c = new Category($info[QueryCache::TITLE]);
                $c->setConfidence($info[QueryCache::CONFIDENCE]);
                return $c;
            }, $realCache->getResults());
        }

        $categoryRepo = $this->getDoctrine()->getRepository('BMNWikiCategorizerFrontendBundle:Category');

        $heap = new CategoryConfidenceMaxHeap();

        $categories = $categoryRepo->findAll();
        foreach ($categories as $category) {
            $confidence = $category->getProbC();
            foreach ($terms as $term)
                $confidence += $category->getProbT($term);
            $category->setConfidence($confidence);
            $heap->insert($category);
        }

        $top = iterator_to_array(new \LimitIterator($heap, 0, 20));

        // save cache row
        $cache->setResults($top);
        $em = $this->getDoctrine()->getManager();
        $em->persist($cache);
        $em->flush();

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
