<?php

namespace Geo\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        $results = $this->getContainer()
            ->get("opap")
            ->fetchAction($output);

    }
}