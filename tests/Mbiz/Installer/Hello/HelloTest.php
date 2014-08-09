<?php
/**
 * User: Andreas Penz
 * Date: 09/08/14
 * Time: 16:42
 */

namespace Mbiz\Installer\Hello;
use Mbiz\Installer\PHPUnit\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mbiz\Installer\Application;

class HelloTest extends TestCase
{
    /**
     * @test
     */
    public function execute()
    {
        $application = $this->getApplication();

        $command = $application->find('hello');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
    }
}