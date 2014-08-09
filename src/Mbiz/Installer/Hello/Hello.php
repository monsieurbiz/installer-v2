<?php

namespace Mbiz\Installer\Hello;

use Mbiz\Installer\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Hello extends BaseCommand
{

    /**
     * Configure the Command
     * @return \Mbiz\Installer\Hello\Hello
     */
    public function configure()
    {
        return $this
            ->setName('hello')
            ->setDescription('Say hello')
            ->addArgument(
                'names',
                InputArgument::IS_ARRAY,
                'Who? (Names separated by space.)'
            )
            ->setTemplatesDirectory(__DIR__ . '/Resources/views/')
        ;
    }

    /**
     * Execute the command
     * @param \Symfony\Component\Console\Input\InputInterface $input The input
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $names = $input->getArgument('names');
        foreach ($names as $name) {
            $output->writeLn($this->render('hello.twig', ['name' => $name]));
        }
    }

}

