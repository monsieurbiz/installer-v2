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

namespace Mbiz\Installer\Block;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper\Helper as Helper;
use Mbiz\Installer\Helper as InstallerHelper;
use Symfony\Component\Console\Input\ArrayInput as ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Adminhtml extends BaseCommand {

    /**
     * Configure the Command
     * @return \Mbiz\Installer\Block\Adminhtml
     */
    public function configure()
    {
        return $this
            ->setName('adminhtml')
            ->setDescription('Create adminhtml section (menu items + acl)')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Helper data
        $command = $this->getApplication()->find('helper');
        $arguments = array(
            'command' => 'helper',
            'name'    => array('data', '-')
        );

        $input = new ArrayInput($arguments);
        $command->run($input, $output);

        $_installerHelper = new InstallerHelper();

        $dir = $_installerHelper->getModuleDir('etc');

        if (!is_file($filename = $dir . '/adminhtml.xml')) {
            file_put_contents($filename, $_installerHelper->getTemplate('adminhtml_xml', array(
                '{module}' => strtolower($_installerHelper->getModuleName())
            )));
        }
    }
}