<?php

namespace Geo\AppBundle\Command;

use MyProject\Proxies\__CG__\stdClass;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class FetchCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('draw:fetch')
            ->setDescription("Fetch opap\'s latest draws");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fetch_service = $this->getContainer()->get("fetch");
        $progress = new ProgressBar($output);

        $progress->setFormat('%message%');
        $progress->setMessage('Loading params. Please wait...');
        $progress->start();

        $progress->setMessage('Loading missing draws...');
        $progress->advance();

        if (!$missingDraws = $fetch_service->getMissingDraws()) {
            $progress->setMessage('No missing draws were found!');
            $progress->advance();
        } else {
            $progress->setMessage("Found " . count($missingDraws) . " missing draws");
            $progress->advance();

            foreach ($missingDraws as $code) {
                $draw = false;
                if ($status = $fetch_service->fetchDraw($code, $draw)) {
                    $progress->setMessage("[SUCC] {$code} - " . json_encode($draw->results));
                    $progress->advance();
                    $fetch_service->saveDraw($draw);
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