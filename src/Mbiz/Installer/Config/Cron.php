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
use Mbiz\Installer\Helper as InstallerHelper;

class Cron extends BaseCommand {

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Ask parts of cron ;)
        // Cron name
        if (empty($params)) {
            do {
                $line = $_installerHelper->prompt('Identifier?');
            } while (empty($line));
        } else {
            $line = array_shift($params);
        }
        $identifier = $line;

        // Minutes
        if (empty($params)) {
            do {
                $line = $_installerHelper->prompt('Minutes?');
            } while ($line === '');
        } else {
            $line = array_shift($params);
        }
        $minutes = $line;

        // Hours
        if (empty($params)) {
            do {
                $line = $_installerHelper->prompt('Hours?');
            } while ($line === '');
        } else {
            $line = array_shift($params);
        }
        $hours = $line;

        // Days (0-31)
        if (empty($params)) {
            do {
                $line = $_installerHelper->prompt('Days? (0-31)');
            } while ($line === '');
        } else {
            $line = array_shift($params);
        }
        $days = $line;

        // Month
        if (empty($params)) {
            do {
                $line = $_installerHelper->prompt('Month?');
            } while ($line === '');
        } else {
            $line = array_shift($params);
        }
        $month = $line;

        // Week days (0-6)
        if (empty($params)) {
            do {
                $line = $_installerHelper->prompt('Days of week?');
            } while ($line === '');
        } else {
            $line = array_shift($params);
        }
        $daysWeek = $line;

        // Model
        if (empty($params)) {
            do {
                $line = $_installerHelper->prompt('Model?');
            } while (empty($line));
        } else {
            $line = array_shift($params);
        }
        $model = $line;

        // Method
        if (empty($params)) {
            do {
                $line = $_installerHelper->prompt('Method?');
            } while (empty($line));
        } else {
            $line = array_shift($params);
        }
        $method = $line;


        // Now the Config
        $_installerHelper = new InstallerHelper();
        $config = $_installerHelper->getConfig();
        if (!isset($config->crontab)) {
            $config->addChild('crontab');
        }
        if (!isset($config->crontab->jobs)) {
            $config->crontab->addChild('jobs');
        }

        // Our cron
        $cron = $config->crontab->jobs->addChild($identifier);
        $cron->addChild('schedule')->addChild('cron_expr');
        $cron->schedule->cron_expr = sprintf('%s %s %s %s %s', $minutes, $hours, $days, $month, $daysWeek);
        $cron->addChild('run')->addChild('model');
        $cron->run->model = sprintf('%s::%s', $model, $method);

        $_installerHelper->writeConfig();
    }
}