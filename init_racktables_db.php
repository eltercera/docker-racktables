<?php

/*
 * This file is part of docker-racktables.
 *
 * docker-racktables is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * docker-racktables is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Foobar.  If not, see <https://www.gnu.org/licenses/>.
 */

function path_join()
{
    $parts = [];
    foreach (func_get_args() as $arg) {
        if (is_array($arg)) {
            array_push($parts, ...$arg);
        } else {
            array_push($parts, strval($arg));
        }
    }
    return implode(DIRECTORY_SEPARATOR, $parts);
}

define('RACKTABLES_PATH', realpath(getenv('RACKTABLES_PATH')));

global $pdo_dsn, $db_username, $db_password, $dbxlink;
global $remote_username, $SQLSchema, $configCache, $script_mode;
$script_mode = TRUE;
require_once path_join(RACKTABLES_PATH, 'wwwroot', 'inc', 'pre-init.php');
require_once path_join(RACKTABLES_PATH, 'wwwroot', 'inc', 'dictionary.php');
require_once path_join(RACKTABLES_PATH, 'wwwroot', 'inc', 'config.php');
require_once path_join(RACKTABLES_PATH, 'wwwroot', 'inc', 'install.php');

//init_database_static();

//$result = init_database_dynamic();
connect_to_db_or_die();
$hash = sha1 (getenv('ADMIN_PASSWD'));
$query = "INSERT INTO `UserAccount` (`user_id`, `user_name`, `user_password_hash`, `user_realname`) " .
    "VALUES (1,'admin','${hash}','RackTables Administrator')";
$result = $dbxlink->exec ($query);