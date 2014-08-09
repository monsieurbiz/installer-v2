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

use \Mbiz\Installer\Application;

if (!class_exists('\Mbiz\Installer\Bootstrap')) {

    /**
     * Bootstrap class
     */
    class Bootstrap
    {

        /**
         * Get a file if it exists
         * @return false | mixed
         */
        public static function includeIfExists($file)
        {
            if (file_exists($file)) {
                return include $file;
            }
        }

        /**
         * Retrieve the class loader
         * @throws ErrorException
         * @return \Composer\Autoload\ClassLoader
         */
        public static function getLoader()
        {
            if ((!$loader = self::includeIfExists(__DIR__ . '/../vendor/autoload.php'))
                && (!$loader = self::includeIfExists(__DIR__ . '/../../../autoload.php'))
            ) {
                // No loader found
                throw new \ErrorException('You must set up the project dependencies, run the following commands:'.PHP_EOL.
                    'curl -s http://getcomposer.org/installer | php'.PHP_EOL.
                    'php composer.phar install'.PHP_EOL);
            }

            return $loader;
        }
    }
}

/*
 * Init the application
 */
try {
    Bootstrap::getLoader();
    $application = new Application();

    return $application;

} catch (\Exception $e) {
    echo $e->getMessage();
    exit(1);
}

