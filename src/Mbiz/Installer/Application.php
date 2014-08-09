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
use Mbiz\Installer\Shell;

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
     * Construct the Application
     */
    public function __construct($runShell = true)
    {
        parent::__construct(self::APP_NAME, self::APP_VERSION);

        $this->_runShell = (bool) $runShell;

        $this->add(new Hello\Hello);
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface  $input  An Input instance
     * @param OutputInterface $output An Output instance
     *
     * @return int 0 if everything went fine, or an error code
     *
     * @throws \Exception When doRun returns Exception
     *
     * @api
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if ($this->_runShell) {
            $this->runShell();
        } else {
            parent::run($input, $output);
        }
    }

    /**
     * Start the Installer's shell
     */
    public function runShell()
    {
        $shell = new Shell($this);
        $shell->run();
    }

}

