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

require_once '/utils.php';

define('RACKTABLES_PATH', realpath(docker_get_config(true, 'RACKTABLES_PATH')));
define('RACKTABLES_SAMPLE_DATA', path_join(RACKTABLES_PATH, 'scripts', 'init-sample-racks.sql'));

global $pdo_dsn, $db_username, $db_password, $dbxlink;
global $remote_username, $SQLSchema, $configCache, $script_mode;
$script_mode = TRUE;
require_once path_join(RACKTABLES_PATH, 'wwwroot', 'inc', 'pre-init.php');
require_once path_join(RACKTABLES_PATH, 'wwwroot', 'inc', 'dictionary.php');
require_once path_join(RACKTABLES_PATH, 'wwwroot', 'inc', 'config.php');
require_once path_join(RACKTABLES_PATH, 'wwwroot', 'inc', 'install.php');

if (! init_database_static()) {
    echo "Error on init db";
    return false;
}
connect_to_db_or_die();
$hash = sha1(docker_get_config(false, 'RACKTABLES_ADMIN_PASSWD', '123456', true));
$query = "INSERT INTO `UserAccount` (`user_id`, `user_name`, `user_password_hash`, `user_realname`) VALUES (?, ?, ?, ?)";
$prepared = $dbxlink->prepare ($query);
$result = $prepared->execute ([1, 'admin', $hash, 'RackTables Administrator']);

if (docker_get_config(false, 'RACKTABLES_INIT_SAMPLE_RACKS')){
    $query = file_get_contents(RACKTABLES_SAMPLE_DATA);
    $dbxlink->exec($query);
}
return true;

