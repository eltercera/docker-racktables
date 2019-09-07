# Supported tags and respective `Dockerfile` links

- [`0.21.3`, `0.21`, `latest`](https://github.com/eltercera/docker-racktables/blob/master/Dockerfile)

# What is Racktables

![Racktables](https://www.racktables.org/img/logo.png)

Racktables is a nifty and robust solution for datacenter and server room asset management. It helps document hardware assets, network addresses, space in racks, networks configuration and much much more!

[www.racktables.org](https://www.racktables.org)

# How to use this image

For create maraidb service:

```
docker run \
    --name mariadb \
    -e MYSQL_ROOT_PASSWORD="SuperAdminPassw" \
    -e MYSQL_DATABASE="racktables" \
    -e MYSQL_USER="racktables" \
    -e MYSQL_PASSWORD="racktablesUserPwd" \
    -p 3306:3306 \
    -d mariadb:latest
```

To initialize database:

```
docker run \
    --rm \
    --link mariadb:mariadb \
    -e RACKTABLES_DB_HOST=mariadb \
    -e RACKTABLES_DB_USERNAME="racktables" \
    -e RACKTABLES_DB_PASSWORD="racktablesUserPwd" \
    -e RACKTABLES_DB_NAME="racktables" \
    -e RACKTABLES_INIT_DB=yes \
    -e RACKTABLES_ADMIN_PASSWD=RacktablesAdminPasswd \
    -it eltercera/docker-racktables:latest
```

Or initialize database whit sample racks data:

```
docker run \
    --rm \
    --link mariadb:mariadb \
    -e RACKTABLES_DB_HOST=mariadb \
    -e RACKTABLES_DB_USERNAME="racktables" \
    -e RACKTABLES_DB_PASSWORD="racktablesUserPwd" \
    -e RACKTABLES_DB_NAME="racktables" \
    -e RACKTABLES_INIT_DB=yes \
    -e RACKTABLES_ADMIN_PASSWD=RacktablesAdminPasswd \
    -e RACKTABLES_INIT_SAMPLE_RACKS=yes \
    -it eltercera/docker-racktables:latest
```

Start service:

```
docker run \
    --name racktables \
    --link mariadb:mariadb \
    -e RACKTABLES_DB_HOST=mariadb \
    -e RACKTABLES_DB_USERNAME=racktables \
    -e RACKTABLES_DB_PASSWORD="racktablesUserPwd" \
    -e RACKTABLES_DB_NAME=racktables \
    -p 8080:8080 \
    -d eltercera/docker-racktables:latest
```

Now, go to [http://localhost:8080/](http://localhost:8080/) and login whit user `admin` and password `RacktablesAdminPasswd`

# Environment variables

## Apache server

* **APACHE_HTTP_PORT:** (Default: "8080") Apache listen port.
* **SERVER_ADMIN:** (Default: "admin@example.com") Apache [ServerAdmin](https://httpd.apache.org/docs/2.4/de/mod/core.html#serveradmin).

## Racktables

* **RACKTABLES_ADMIN_PASSWD:** (Default: "123456") To ser Admin user password on database init.
* **RACKTABLES_HELPDESK_BANNER:** This HTML banner is intended to assist users in dispatching their issues to the local tech support service. Its text (in its verbatim form) will be appended to assorted error messages visible in user's browser (including "not authenticated" message). Beware of placing any sensitive information here, it will be readable by unauthorized visitors.
* **RACKTABLES_PLUGINS_DIR:** (Default: "/racktables/plugins") Set this if you need to override the default plugins directory.
* **RACKTABLES_REQUIRE_LOCAL_ACCOUNT:** (Default: "true")
* **RACKTABLES_USER_AUTH_SRC:** (Default: "database") Authetication Source

## Database

* **RACKTABLES_DB_HOST:** (Default: "mariadb") Hostname for database connection.
* **RACKTABLES_DB_NAME:** (Default: "racktables") Database name.
* **RACKTABLES_DB_PASSWORD:** (Default: "racktables") User password for database connection.
* **RACKTABLES_DB_PORT:** (Default: "3306") TCP port for database connection.
* **RACKTABLES_DB_USERNAME:** (Default: "racktables") User name for database connection.
* **RACKTABLES_INIT_DB:** Set any value to try initialize database.
* **RACKTABLES_INIT_SAMPLE_RACKS:** set any value for inser sample data to  database.
* **RACKTABLES_PDO_BUFSIZE:** Setting MySQL client buffer size may be required to make downloading work folarger files.
* **RACKTABLES_PDO_SSL_CA:** PDO SSL ca path.
* **RACKTABLES_PDO_SSL_CERT:** PDO SSL cert path.
* **RACKTABLES_PDO_SSL_KEY:** PDO SSL key path.

## LDAP AUTH

See: [https://wiki.racktables.org/index.php/LDAP](https://wiki.racktables.org/index.php/LDAP)

* **RACKTABLES_LDAP_CACHE_EXPIRY**
* **RACKTABLES_LDAP_CACHE_REFRESH**
* **RACKTABLES_LDAP_CACHE_RETRY**
* **RACKTABLES_LDAP_DISPLAYNAME_ATTRS**
* **RACKTABLES_LDAP_DOMAIN**
* **RACKTABLES_LDAP_GROUP_ATTR**
* **RACKTABLES_LDAP_GROUP_FILTER**
* **RACKTABLES_LDAP_OPTIONS** Use Json format for pass options.
* **RACKTABLES_LDAP_PORT**
* **RACKTABLES_LDAP_SEARCH_ATTR**
* **RACKTABLES_LDAP_SEARCH_BIND_PASSWORD**
* **RACKTABLES_LDAP_SEARCH_BIND_RDN**
* **RACKTABLES_LDAP_SEARCH_DN**
* **RACKTABLES_LDAP_SERVER** (required)
* **RACKTABLES_LDAP_USE_TLS**


