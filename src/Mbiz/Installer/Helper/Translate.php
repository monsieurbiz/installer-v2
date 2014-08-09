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
namespace Mbiz\Installer\Helper;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper\Helper as Helper;
use Mbiz\Installer\Helper as InstallerHelper;
use Symfony\Component\Console\Input\ArrayInput as ArrayInput;

class Translate extends BaseCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $_installerHelper = new InstallerHelper();

        $params = $input->getParams();
        if (!empty($params) && in_array($params[0], array('admin', 'front'))) {
            $where = $params[0];
        } else {
            do {
                $where = $_installerHelper->prompt('Where? (enter for front)');
                if (empty($where)) {
                    $where = 'front';
                }
            } while (!in_array($where, array('admin', 'front')));
        }

        if ($where == 'admin') {
            $where = 'adminhtml';
        } else {
            $where = 'frontend';
        }

        $config = $_installerHelper->getConfig();

        if (!isset($config->{$where})) {
            $config->addChild($where);
        }

        if (!isset($config->{$where}->translate)) {
            // Helper data
            $command = $this->getApplication()->find('helper');
            $arguments = array(
                'command' => 'helper',
                'params'    => array('data', '-')
            );

            $input = new ArrayInput($arguments);
            $command->run($input, $output);
            $config->{$where}
                ->addChild('translate')
                ->addChild('modules')
                ->addChild($_installerHelper->getModuleName())
                ->addChild('files')
                ->addChild('default', $_installerHelper->getModuleName() . '.csv');
            $_installerHelper->writeConfig();

            foreach ($_installerHelper->getLocales() as $locale) {
                $dir = $_installerHelper->getAppDir() . 'locale/' . $locale . '/';
                if (!is_dir($dir)) {
                    mkdir($dir);
                }
                touch($dir . $_installerHelper->getModuleName() . '.csv');
            }
        }

        $_installerHelper->_processReloadConfig();

        $_installerHelper->setLast(__FUNCTION__);
    }
}