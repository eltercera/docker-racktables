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
define('RACKTABLES_SECRET_PATH', path_join(RACKTABLES_PATH, 'wwwroot', 'inc', 'secret.php'));

/**
 * Process var string type
 * @return string | null
 */
function docker_proc_config_str()
{
    $val = docker_get_config(...func_get_args());
    return is_null($val)?null:strval($val);
}

/**
 * Process var bool type
 * @return bool | null
 */
function docker_proc_config_bool()
{
    $val = docker_get_config(...func_get_args());
    return is_null($val)?null:boolval($val);
}

/**
 * Process var int type
 * @return integer | null
 */
function docker_proc_config_int()
{
    $val = docker_get_config(...func_get_args());
    return is_null($val)?null:intval($val);
}

/**
 * Proces value of $LDAP_options['options']
 * @param bool $required
 * @param string $env_name
 * @return array | null
 */
function docker_proc_config_ldapopt($required, $env_name)
{
    $data_json = docker_get_config($required, $env_name);
    $ret = [];
    $data = json_decode($data_json, true);
    if(is_array($data)) {
        foreach ($data as $constant => $value) {
            $ret[constant($constant)] = $value;
        }
    }
    if(! $ret) {
        return null;
    }
    return $ret;
}

/**
 * Process dsn pdo string
 * @return string
 */
function docker_proc_config_pdo_dsn()
{
    $host = docker_get_config(false, 'RACKTABLES_DB_HOST', 'mariadb');
    $dbname = docker_get_config(false, 'RACKTABLES_DB_NAME', 'racktables');
    $dbport = docker_get_config(false, 'RACKTABLES_DB_PORT', '3306');
    return "mysql:host=$host;dbname=$dbname;port=$dbport";
}

/**
 * process config var.
 * @param string $func name of function to process var
 * @param string ...$args rest of arguments for call function to process var
 * @return mixed
 */
function docker_proc_config()
{
    $args = func_get_args();
    $func = array_shift($args);
    if (! function_exists($func)) {
        echo "Function $func not exists" . PHP_EOL;
        exit(11);
    }
    return call_user_func_array($func, $args);
}

/**
 * Process var array type
 * @param bool $required
 * @param array $vars list other vars
 * @return array | null
 */
function docker_proc_config_array($required, $vars)
{
    $ret = [];
    foreach ($vars as $key => $args) {
        $requi = $args[1];
        $args[1] = false;
        $value = docker_proc_config(...$args);
        if(is_null($value)) {
            if($requi && $required) {
                echo "$key env var is required" . PHP_EOL;
                exit(10);
            }
        } else {
            $ret[$key] = $value;
        }
    }
    if (! $ret) {
        if ($required) {
            echo "Array value required" . PHP_EOL;
            exit(12);
        }
        return null;
    }
    return $ret;
}

/**
 * Generate racktables secret.php file from values on env vars
 * @return boolean
 */
function docker_make_secret()
{
    /* List of all vars to proc */
    $required_settings = [
        'pdo_dsn' => ['docker_proc_config_pdo_dsn'],
        'db_username' => ['docker_proc_config_str', false, 'RACKTABLES_DB_USERNAME', 'racktables'],
        'db_password' => ['docker_proc_config_str', false, 'RACKTABLES_DB_PASSWORD', 'racktables', true],
        'user_auth_src' => ['docker_proc_config_str', false, 'RACKTABLES_USER_AUTH_SRC', 'database'],
        'require_local_account' => ['docker_proc_config_bool', false, 'RACKTABLES_REQUIRE_LOCAL_ACCOUNT', true],
        'racktables_plugins_dir' => ['docker_proc_config_str', false, 'RACKTABLES_PLUGINS_DIR'],
        'pdo_bufsize' => ['docker_proc_config_int', false, 'RACKTABLES_PDO_BUFSIZE'],
        'pdo_ssl_key' => ['docker_proc_config_str', false, 'RACKTABLES_PDO_SSL_KEY'],
        'pdo_ssl_cert' => ['docker_proc_config_str', false, 'RACKTABLES_PDO_SSL_CERT'],
        'pdo_ssl_ca' => ['docker_proc_config_str', false, 'RACKTABLES_PDO_SSL_CA'],
        'helpdesk_banner' => ['docker_proc_config_str', false, 'RACKTABLES_HELPDESK_BANNER'],
        'LDAP_options' => ['docker_proc_config_array', false, [
            'server' => ['docker_proc_config_str', true, 'RACKTABLES_LDAP_SERVER'],
            'port' => ['docker_proc_config_str', false, 'RACKTABLES_LDAP_PORT'],
            'domain' => ['docker_proc_config_str', false, 'RACKTABLES_LDAP_DOMAIN'],
            'search_attr' => ['docker_proc_config_str', false, 'RACKTABLES_LDAP_SEARCH_ATTR'],
            'search_dn' => ['docker_proc_config_str', false, 'RACKTABLES_LDAP_SEARCH_DN'],
            'search_bind_rdn' => ['docker_proc_config_str', false, 'RACKTABLES_LDAP_SEARCH_BIND_RDN'],
            'search_bind_password' => ['docker_proc_config_str', false, 'RACKTABLES_LDAP_SEARCH_BIND_PASSWORD', null, true],
            'displayname_attrs' => ['docker_proc_config_str', false, 'RACKTABLES_LDAP_DISPLAYNAME_ATTRS'],
            'group_attr' => ['docker_proc_config_str', false, 'RACKTABLES_LDAP_GROUP_ATTR'],
            'group_filter' => ['docker_proc_config_str', false, 'RACKTABLES_LDAP_GROUP_FILTER'],
            'cache_refresh' => ['docker_proc_config_int', false, 'RACKTABLES_LDAP_CACHE_REFRESH'],
            'cache_retry' => ['docker_proc_config_int', false, 'RACKTABLES_LDAP_CACHE_RETRY'],
            'cache_expiry' => ['docker_proc_config_int', false, 'RACKTABLES_LDAP_CACHE_EXPIRY'],
            'options' => ['docker_proc_config_ldapopt', false, 'RACKTABLES_LDAP_OPTIONS'],
            'use_tls' => ['docker_proc_config_int', false, 'RACKTABLES_LDAP_USE_TLS']
        ]],
        ## TODO
        'SAML_options' => ['docker_proc_config_array', false, [
            'simplesamlphp_basedir' => ['docker_proc_config_str', true, 'RACKTABLES_SAML_SIMPLESAMLPHP_BASEDIR'],
            'sp_profile' => ['docker_proc_config_str', true, 'RACKTABLES_SAML_SP_PROFILE'],
            'usernameAttribute' => ['docker_proc_config_str', true, 'RACKTABLES_SAML_USERNAMEATTRIBUTE'],
            'fullnameAttribute' => ['docker_proc_config_str', true, 'RACKTABLES_SAML_FULLNAMEATTRIBUTE'],
        ]]
    ];

    if (file_exists(RACKTABLES_SECRET_PATH) && ! is_writable(RACKTABLES_SECRET_PATH)) {
        echo "File " . RACKTABLES_SECRET_PATH . " is not writable." . PHP_EOL;
        return false;
    }

    $file_data = '<?php' . PHP_EOL;

    foreach ($required_settings as $var_name => $args) {
        $value = docker_proc_config(...$args);
        if(! is_null($value)) {
            $file_data .= '$'. $var_name . ' = ' . var_export($value, TRUE) . ';' . PHP_EOL;
        }
    }

    $bytes = file_put_contents(RACKTABLES_SECRET_PATH, $file_data);

    if (! $bytes) {
        echo "Error on write file " . RACKTABLES_SECRET_PATH . PHP_EOL;
        return false;
    }
    return true;
}

docker_make_secret();
