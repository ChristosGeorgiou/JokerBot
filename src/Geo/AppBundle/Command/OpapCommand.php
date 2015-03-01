<?php

namespace Geo\AppBundle\Command;

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
        $progress = new ProgressBar($output);

        $progress->setFormat('%message%');
        //$progress->setFormat('[ %percent:3s%% - %elapsed:8s%/%estimated:-8s% - MEM: %memory:7s% ] %message%');

        $progress->setMessage('Loading params. Please wait...');
        $progress->start();

        $results = $this->getContainer()->get("opap")->fetchAction($progress);

        $progress->setMessage('Completed');
        $progress->advance();

        $progress->finish();

    }
}