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

use Symfony\Component\Console\Shell as BaseShell;

class Shell extends BaseShell
{

    /**
     * The application
     * @var Application
     */
    private $_application;

    /**
     * The logo of the Installer
     * @var string
     */
    private $_logo = "
  _____           _        _ _                  ___  
 |_   _|         | |      | | |                |__ \ 
   | |  _ __  ___| |_ __ _| | | ___ _ __  __   __ ) |
   | | | '_ \/ __| __/ _` | | |/ _ \ '__| \ \ / // / 
  _| |_| | | \__ \ || (_| | | |  __/ |     \ V // /_ 
 |_____|_| |_|___/\__\__,_|_|_|\___|_|      \_/|____|
                                                     
    ";

    /**
     * Constructor.
     *
     * If there is no readline support for the current PHP executable
     * a \RuntimeException exception is thrown.
     *
     * @param Application $application An application instance
     */
    public function __construct(Application $application)
    {
        $this->_application = $application;

        parent::__construct($application);
    }

    /**
     * Retrieve the Header text
     * @return string
     */
    protected function getHeader()
    {
        return <<<EOF
{$this->_logo}
Welcome to the <info>{$this->_application->getName()}</info> shell (<comment>{$this->_application->getVersion()}</comment>).

At the prompt, type <comment>help</comment> for some help,
or <comment>list</comment> to get a list of available commands.

To exit the shell, type <comment>^D</comment>.

EOF;
    }

}
