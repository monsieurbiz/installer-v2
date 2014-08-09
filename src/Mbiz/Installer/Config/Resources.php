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
namespace Mbiz\Installer\Config;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallationHelper;

class Resources extends BaseCommand
{
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $_installationHelper = new InstallationHelper();
        list($dir, $created) = $_installationHelper->_createModelDir();

        $config = $_installationHelper->getConfig();
        $models = $config->global->models;

        if (!$models->{strtolower($_installationHelper->getModuleName())}->resourceModel) {
            $models->{strtolower($_installationHelper->getModuleName())}->addChild('resourceModel', strtolower($_installationHelper->getModuleName()) . '_resource');
            $resource = $models->addChild(strtolower($_installationHelper->getModuleName()) . '_resource');
            $resource->addChild('class', $_installationHelper->getModuleName() . '_Model_Resource');
            $resource->addChild('entities');
            $_installationHelper->writeConfig();
            @mkdir($dir = $dir . 'Resource/');
        }

        $this->_processReloadConfig();


        $_installationHelper->setLast(__FUNCTION__);
    }
}