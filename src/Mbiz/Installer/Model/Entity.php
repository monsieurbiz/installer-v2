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

namespace Mbiz\Installer\Model\Entity;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Config\Resources as Resources;
use Mbiz\Installer\Helper as InstallationHelper;

class Entity
{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $_resources = new Resources();
        $_resources->execute(array());

        if (empty($params)) {
            do {
                $name = ucfirst($this->prompt('Entity?'));
            } while (empty($name));
        } else {
            $name = ucfirst(array_shift($params));
        }

        if (empty($params)) {
            do {
                $table = $this->prompt('Table?');
            } while (empty($table));
        } else {
            $table = array_shift($params);
        }

        $noFiles = false;
        if (!empty($params)) {
            $noFiles = (array_shift($params) == '-');
        }

        $config = $this->getConfig();
        if (!isset($config->global)) {
            $config->addChild('global');
        }
        $entities = $config->global->models->{strtolower($this->getModuleName() . '_resource')}->entities;

        $entity = implode('_', array_map('ucfirst', explode('_', $name)));

        $names = explode('_', $entity);
        $lastName = array_pop($names);
        $filename = $lastName . '.php';

        if ($entities->{strtolower($name)}) {
            echo red() . "Entity $entity already exist.\n" . white();
            $this->_processReloadConfig();
            return;
        }

        $entities->addChild(strtolower($name))->addChild('table', $table);
        $this->writeConfig();

        $dir = $this->getModuleDir('Model');

        $construct = $this->getTemplate('entity_class_construct', array(
            '{Entity}' => $entity,
            '{entity}' => strtolower($entity),
            '{resourceModel}' => strtolower($this->getModuleName() . '/' . $entity)
        ));

        foreach ($names as $name) {
            if (!is_dir($dir = $dir . $name . '/')) {
                if (!$noFiles) {
                    mkdir($dir);
                }
            }
        }

        if (!$noFiles) {
            file_put_contents($dir . $filename, $this->getTemplate('model_class', array(
                '{Name}' => $entity,
                $this->getTag('new_method') => $construct . "\n\n" . $this->getTag('new_method')
            )));
        }

        $dir = $this->getModuleDir('Model') . 'Resource/';

        foreach ($names as $name) {
            if (!is_dir($dir = $dir . $name . '/')) {
                if (!$noFiles) {
                    mkdir($dir);
                }
            }
        }

        if (!$noFiles) {
            file_put_contents($dir . $filename, $this->getTemplate('mysql4_entity_class', array(
                '{Name}' => $entity,
                '{mainTable}' => strtolower($this->getModuleName() . '/' . $entity),
                '{idField}' => strtolower($lastName) . '_id'
            )));
        }

        if (!is_dir($dir = $dir . $lastName . '/')) {
            if (!$noFiles) {
                mkdir($dir);
            }
        }
        if (!$noFiles) {
            file_put_contents($dir . 'Collection.php', $this->getTemplate('mysql4_collection_class', array(
                '{Name}' => $entity,
                '{model}' => strtolower($this->getModuleName() . '/' . $entity)
            )));
        }

        $this->_processReloadConfig();

        $_installationHelper = new InstallationHelper();
        $_installationHelper->setLast(__FUNCTION__);
    }
}