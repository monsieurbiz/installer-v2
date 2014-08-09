<?php
/**
 * User: Andreas Penz
 * Date: 09/08/14
 * Time: 20:20
 */

use \Mbiz\Installer\Shell;
use Mbiz\Installer\PHPUnit\TestCase;

class ShellTest extends TestCase
{
    public function getInstance()
    {
        return new Shell( $this->getApplication() );
    }

    /**
     * @test
     * @covers Mbiz\Installer\Shell::getPrompt
     */
    public function getPromptReturnsFromProperty()
    {

        $instance = $this->getInstance();

        $reflection = new ReflectionClass( get_class( $instance ));
        $method = $reflection->getMethod("getPrompt");
        $method->setAccessible(true);

        $property = $reflection->getProperty('_prompt');
        $property->setAccessible(true);

        $property->setValue($instance, 'Unicorn');

        $this->assertEquals('Unicorn > ', $method->invoke($instance));

    }

    /**
     * @test
     * @covers Mbiz\Installer\Shell::getPrompt
     */
    public function getPrompt()
    {
        $instance = $this->getInstance();

        $reflection = new ReflectionClass( get_class( $instance ));
        $method = $reflection->getMethod("getPrompt");
        $method->setAccessible(true);

        $property = $reflection->getProperty('_prompt');
        $property->setAccessible(true);

        $property->setValue($instance, null);

        $this->assertEquals('Installer > ', $method->invoke($instance));

    }

}