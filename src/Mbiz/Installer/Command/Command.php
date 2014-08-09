<?php

namespace Mbiz\Installer\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{

    /**
     * @var
     */
    protected $_twig;

    /**
     * @var
     */
    protected $_templatesDirectory;

    /**
     * @param $dir
     * @return $this
     */
    public function setTemplatesDirectory($dir)
    {
        $loader = new \Twig_Loader_Filesystem($dir);
        $twig = new \Twig_Environment($loader, []);
        $this->setTwig($twig);
        return $this;
    }

    /**
     * @param $name
     * @param array $context
     * @return mixed
     */
    public function render($name, array $context = array())
    {
        return $this->getTwig()->render($name, $context);
    }

    /**
     * @return mixed
     */
    public function getTwig()
    {
        if(is_null($this->_twig)) {
           throw new \RuntimeException('\Twig_Environment instance is not set.');
        }
        return $this->_twig;
    }

    /**
     * @param mixed $twig
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->_twig = $twig;
    }


}
