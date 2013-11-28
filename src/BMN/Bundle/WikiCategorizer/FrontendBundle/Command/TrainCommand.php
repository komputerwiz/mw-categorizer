<?php

namespace BMN\Bundle\WikiCategorizer\FrontendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

use BMN\Bundle\WikiCategorizer\FrontendBundle\Entity\Category;
use BMN\Bundle\WikiCategorizer\FrontendBundle\Entity\Proxy\NaiveBayesCategoryBuilderProxy as CategoryProxy;

class TrainCommand extends ContainerAwareCommand
{
    private $Pc;
    private $Ptc;

    protected function configure()
    {
        $this
            ->setName('mwcategorizer:train')
            ->setDescription('Load training data from file')
            ->addArgument('file', InputArgument::REQUIRED, 'JSON training file: [{text: "", categories: []}]')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $fs = new Filesystem();
        if (!$fs->exists($file))
            throw new \RuntimeException('Input file does not exist: ' . $file);

        $articles = json_decode(file_get_contents($file), true);

        $tokenizer = $this->getContainer()->get('mediawiki_tokenizer');

        $output->writeln(sprintf('Loaded %s', basename($file)));
        $output->writeln('Processing articles');

        $progress = $this->getHelperSet()->get('progress');
        $progress->start($output, count($articles));

        // collect training data: count stuff
        $proxies = array();
        foreach ($articles as $article) {
            // tokenize text
            $words = $tokenizer->tokenize($article['text']);

            foreach ($article['categories'] as $cat) {
                // track which words belong to which category for P(t|c)
                // track number of articles in each category for P(c)
                $cp = isset($proxies[$cat]) ? $proxies[$cat] : new CategoryProxy($cat);
                $proxies[$cat] = $cp->addWords($words)->incrementDocCounter();
            }

            $progress->advance();
        }
        $progress->finish();

        $translator = $this->getContainer()->get('translator');
        $output->writeln($translator->transChoice(
            '{0} Trained with no categories|{1} Trained with one category|]1,Inf[ Trained with %count% categories',
            count($proxies),
            array('%count%' => count($proxies))
        ));
        $output->writeln('Saving training data to database');

        // save generated category data
        $progress->start($output, count($proxies));
        $em = $this->getContainer()->get('doctrine')->getManager();
        $i = 0;
        foreach ($proxies as $proxy) {
            $category = $proxy->getCategory();
            $em->persist($category);
            // flush every so often to prevent backlog
            if (0 === $i++ % 100) $em->flush();
            $progress->advance();
        }
        $em->flush();
        $progress->finish();
        $output->writeln('cleaning up');
    }
}
