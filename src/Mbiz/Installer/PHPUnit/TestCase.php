<?php
/**
 * User: Andreas Penz
 * Date: 09/08/14
 * Time: 18:15
 */

namespace Mbiz\Installer\PHPUnit;
use Mbiz\Installer\Application;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mbiz\Installer\Application
     */
    private $application = null;

    /**
     * @throws \RuntimeException
     * @return \Mbiz\Installer\Application
     */
    public function getApplication()
    {
        if ($this->application === null) {
            $this->application = new Application(false);
        }

        return $this->application;
    }
}