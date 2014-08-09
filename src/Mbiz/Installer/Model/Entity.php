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

namespace Mbiz\Installer\Model;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallerHelper;

class Entity
{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $params = $input->getParams();

        $command = $this->getApplication()->find('resources');
        $command->run($input, $output);

        $_installerHelper = new InstallerHelper();

        if (empty($params)) {
            do {
                $name = ucfirst($_installerHelper->prompt('Entity?'));
            } while (empty($name));
        } else {
            $name = ucfirst(array_shift($params));
        }

        if (empty($params)) {
            do {
                $table = $_installerHelper->prompt('Table?');
            } while (empty($table));
        } else {
            $table = array_shift($params);
        }

        $noFiles = false;
        if (!empty($params)) {
            $noFiles = (array_shift($params) == '-');
        }

        $config = $_installerHelper->getConfig();
        if (!isset($config->global)) {
            $config->addChild('global');
        }
        $entities = $config->global->models->{strtolower(getModuleName() . '_resource')}->entities;

        $entity = implode('_', array_map('ucfirst', explode('_', $name)));

        $names = explode('_', $entity);
        $lastName = array_pop($names);
        $filename = $lastName . '.php';

        if ($entities->{strtolower($name)}) {
            echo red() . "Entity $entity already exist.\n" . white();
            $_installerHelper->_processReloadConfig();
            return;
        }

        $entities->addChild(strtolower($name))->addChild('table', $table);
        $_installerHelper->writeConfig();

        $dir = $_installerHelper->getModuleDir('Model');

        $construct = $_installerHelper->getTemplate('entity_class_construct', array(
            '{Entity}' => $entity,
            '{entity}' => strtolower($entity),
            '{resourceModel}' => strtolower($_installerHelper->getModuleName() . '/' . $entity)
        ));

        foreach ($names as $name) {
            if (!is_dir($dir = $dir . $name . '/')) {
                if (!$noFiles) {
                    mkdir($dir);
                }
            }
        }

        if (!$noFiles) {
            file_put_contents($dir . $filename, $_installerHelper->getTemplate('model_class', array(
                '{Name}' => $entity,
                $_installerHelper->getTag('new_method') => $construct . "\n\n" . $_installerHelper->getTag('new_method')
            )));
        }

        $dir = $_installerHelper->getModuleDir('Model') . 'Resource/';

        foreach ($names as $name) {
            if (!is_dir($dir = $dir . $name . '/')) {
                if (!$noFiles) {
                    mkdir($dir);
                }
            }
        }

        if (!$noFiles) {
            file_put_contents($dir . $filename, $_installerHelper->getTemplate('mysql4_entity_class', array(
                '{Name}' => $entity,
                '{mainTable}' => strtolower($_installerHelper->getModuleName() . '/' . $entity),
                '{idField}' => strtolower($lastName) . '_id'
            )));
        }

        if (!is_dir($dir = $dir . $lastName . '/')) {
            if (!$noFiles) {
                mkdir($dir);
            }
        }
        if (!$noFiles) {
            file_put_contents($dir . 'Collection.php', $_installerHelper->getTemplate('mysql4_collection_class', array(
                '{Name}' => $entity,
                '{model}' => strtolower($_installerHelper->getModuleName() . '/' . $entity)
            )));
        }

        $_installerHelper->_processReloadConfig();

        $_installerHelper->setLast(__FUNCTION__);
    }
}