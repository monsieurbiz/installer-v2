<?php
/**
 * Created by PhpStorm.
 * User: mothership
 * Date: 09/08/14
 * Time: 10:22
 */

namespace Mbiz\Installer;
use Mbiz\Installer\PHPUnit\TestCase;

class ApplicationTest extends TestCase
{

    /**
     * @test
     */
    public function execute()
    {
        $application = $this->getApplication();
        /* @var $application Application */
        $this->assertInstanceOf('\Mbiz\Installer\Application', $application);
    }

}