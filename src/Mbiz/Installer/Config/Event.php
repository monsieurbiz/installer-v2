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

namespace Mbiz\Installer\Config\Event;

use Mbiz\Installer\Command\Command as BaseCommand;
use Mbiz\Installer\Helper as InstallationHelper;

class Event{

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty($params)) {
            do {
                $eventName = $this->prompt('Event Name?');
            } while (empty($eventName));
        } else {
            $eventName = array_shift($params);
        }

        if (empty($params)) {
            do {
                $name = $this->prompt('Observer Name?');
            } while (empty($name));
        } else {
            $name = array_shift($params);
        }

        if (empty($params)) {
            do {
                $class = $this->prompt('Model Class?');
            } while (empty($class));
        } else {
            $class = array_shift($params);
        }

        if (empty($params)) {
            do {
                $method = $this->prompt('Method?');
            } while (empty($method));
        } else {
            $method = array_shift($params);
        }

        if (!empty($params)) {
            $where = array_shift($params);
        }
        while (!isset($where) || !in_array($where, array('front', 'admin', 'global'))) {
            $where = $this->prompt('Where? (enter for front)');
            if (empty($where)) {
                $where = 'front';
            }
        }
        if ($where == 'front') {
            $where = 'frontend';
        } elseif ($where == 'admin') {
            $where = 'adminhtml';
        } elseif ($where == 'global') {
            $where = 'global';
        }

        // Config
        $config = $this->getConfig();
        if (!isset($config->{$where})) {
            $config->addChild($where);
        }
        $where = $config->{$where};

        // Events
        if (!$events = $where->events) {
            $events = $where->addChild('events');
        }

        // Event
        if (!$event = $events->{$eventName}) {
            $event = $events->addChild($eventName);
            $observers = $event->addChild('observers');
        } else {
            $observers = $event->observers;
        }

        // Observers
        if (!$observer = $observers->{$name}) {
            $observer = $observers->addChild($name);
            $observer->addChild('class');
            $observer->addChild('method');
        }

        $observer->class = $class;
        $observer->method = $method;

        $this->writeConfig();

        $_installationHelper = new InstallationHelper();
        $_installationHelper->setLast(__FUNCTION__);
    }
}