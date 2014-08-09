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

namespace Mbiz\Installer\Core;

use Mbiz\Installer\Command\Command as BaseCommand;

class Help extends BaseCommand {

    public function execute(InputInterface $input, OutputInterface $output)
    {
        echo white();
        echo <<<HELP
  ---------------------- ----------------------- -------------------------------------------
 | COMMAND              | ALIAS                 | PARAMETERS                                |
 |----------------------|-----------------------|-------------------------------------------|
 | help                 | -h ?                  |                                           |
 | module               | mod                   | namespace name pool                       |
 | general              |                       |                                           |
 | info                 | i config conf         |                                           |
 | clean                |                       | [all, cache, log(s)]                      |
 | controller           | c                     | name [actions]                            |
 | helper               | h                     | name [methods]                            |
 | model                | m                     | name [methods]                            |
 | observer             | o                     | name [methods]                            |
 | observer Observer    | oo                    | [methods]                                 |
 | block                | b                     | name [methods] [-p]                       |
 | translate            | t                     | where                                     |
 | translates           | ts                    |                                           |
 | layout               | l                     | where                                     |
 | layouts              | ls                    |                                           |
 | resources            | res                   |                                           |
 | entity               | ent                   | name table                                |
 | grid                 |                       | entity                                    |
 | form                 |                       | entity                                    |
 |----------------------|-----------------------|-------------------------------------------|
 | COMMAND              | ALIAS                 | PARAMETERS                                |
 |----------------------|-----------------------|-------------------------------------------|
 | setup                | sql set               |                                           |
 | data                 |                       | [[from] to]                               |
 | upgrade              | up                    | [from] to                                 |
 | event                |                       | name model method where                   |
 | cron                 |                       | identifier 1 2 3 4 5 model method         |
 | default              | def conf              | name value                                |
 | depends              | dep                   | (-)module                                 |
 | exit                 |                       |                                           |
 | delete               | del rm remove         |                                           |
 | last                 |                       | [...]                                     |
 | addtranslate         | __                    |                                           |
 | routers              | r route router        | where frontName                           |
 | tmp                  |                       | action                                    |
 | misc                 | script                | name (without .php)                       |
 | doc                  |                       | [title]                                   |
 | system               |                       |                                           |
 | adminhtml            |                       |                                           |
 | session              |                       | [methods]                                 |
 | email                | mail                  | name                                      |
 |                      |                       |                                           |
  ---------------------- ----------------------- -------------------------------------------

HELP;
    }
}