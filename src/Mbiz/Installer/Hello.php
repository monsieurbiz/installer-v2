<?php

namespace Mbiz\Installer;

use Symfony\Component\Console\Command\Command as BasicCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Hello extends BasicCommand
{

    public function configure()
    {
        $this
            ->setName('hello')
            ->setDescription('Say hello')
        ;
    }

    /**
     * Execute the command
     * @param \Symfony\Component\Console\Input\InputInterface $input The input
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Hello World!</info>");
    }

}

