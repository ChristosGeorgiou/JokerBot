<?php

namespace Geo\AppBundle\Command;

use MyProject\Proxies\__CG__\stdClass;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class OpapCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('opap:fetch')
            ->setDescription("Fetch opap\'s latest draw");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $opapservice = $this->getContainer()->get("opap");
        $progress = new ProgressBar($output);

        $progress->setFormat('%message%');
        $progress->setMessage('Loading params. Please wait...');
        $progress->start();

        $progress->setMessage('Loading missing draws...');
        $progress->advance();

        if (!$missingDraws = $opapservice->getMissingDraws()) {
            $progress->setMessage('No missing draws were found!');
            $progress->advance();
        } else {
            $progress->setMessage("Found " . count($missingDraws) . " missing draws");
            $progress->advance();

            foreach ($missingDraws as $code) {
                $draw = new stdClass();
                if ($status = $opapservice->fetchDraw($code, $draw)) {
                    $progress->setMessage("[SUCC] {$code} - " . json_encode($draw->results));
                    $progress->advance();
                    $opapservice->saveDraw($draw);
                } else {
                    $progress->setMessage("[FAIL] {$code}");
                    $progress->advance();
                }
            }
        }

        $progress->setMessage('Completed');
        $progress->advance();
        $progress->finish();
    }
}