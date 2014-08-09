<?php
/**
 * This file is part of Installer version 2.
 *
 * Installer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Installer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Installer.  If not, see <http://www.gnu.org/licenses/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/gpl-3.0.txt>.
 */

namespace Mbiz\Installer;

use Symfony\Component\Console\Application as BaseApplication;
use Mbiz\Installer\Magento\Module;

class Application extends BaseApplication
{

    /**
     * Name of the Application
     * @const APP_NAME string
     */
    const APP_NAME = 'Installer';

    /**
     * Version of the Application
     * @const APP_VERSION string
     */
    const APP_VERSION = '2.0.0@dev';

    /**
     * Run the shell
     * @var bool
     */
    protected $_runShell = true;

    /**
     * The shell
     * @var \Mbiz\Installer\Shell
     */
    protected $_shell;

    /**
     * Construct the Application
     */
    public function __construct($runShell = true)
    {
        parent::__construct(self::APP_NAME, self::APP_VERSION);

        $this->_runShell = (bool) $runShell;

        // Init the basic module
        $this->_module = new Module;

        // Commands
        $this->add(new Hello\Hello);
        $this->add(new Core\Unicorn);
        $this->add(new Core\Module);
        $this->add(new Helper\Helper);

        //---------- Block----------
        $this->add(new Block\Adminhtml);
        //$this->add(new Block\Block);
        //$this->add(new Block\Email);
        //$this->add(new Block\Form);
        //$this->add(new Block\Grid);

        //---------- Block----------
        //$this->add(new Config\Cron);
        //$this->add(new Config\Data);*/

        //---------- Misc----------
        $this->add(new Misc\Doc);

        // Run the Installer shell
        $this->runShell();
    }

    /**
     * Start the Installer's shell
     */
    public function runShell()
    {
        if ($this->_runShell) {
            if (!$this->_shell) {
                $this->_shell = new Shell($this);
            }
            $this->_shell->run();
        }
    }

    /**
     * Set the shell prompt
     * @param string $prompt
     * @return \Mbiz\Installer\Application
     */
    public function setShellPrompt($prompt)
    {
        if ($this->_shell !== null) {
            $this->_shell->setPrompt($prompt);
        }
        return $this;
    }

    /**
     * Retrieve the module
     * @return \Mbiz\Installer\Magento\Module
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Init the next module
     */
    public function initModule($vendor, $module, $pool)
    {
        $this->setShellPrompt(sprintf('%s_%s in %s', $vendor, $module, $pool));
        $this->getModule()->reinit($vendor, $module, $pool);
    }

}

