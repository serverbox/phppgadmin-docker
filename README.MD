# Dockerized build of phpPgAdmin with multi server support

The work was inspired by [phpmyadmin/docker](https://github.com/phpmyadmin/docker)
and it's ability to support multiple database servers.

The initial driver is from an k8s environment on AWS making use of multiple RDS instances.
To provide a phpPgAdmin interface I was forced to roll out the following for each RDS instance
* Ingress
* Pod with containers
As the environment uses helm there is a common chart with a value file for each.

For one or two instances that's fine. For a dozen it's a little tedious.

## Initial sources
### Straight copies
These files are a lift and shift.
* Same path from [phpmyadmin/docker](https://github.com/phpmyadmin/docker)
  * `etc/php-fpm.conf`
  * `etc/php.ini`
  * `etc/supervisor.d/nginx.ini`
  * `etc/supervisor.d/php.ini`

### Minor modifications
These files have had only minor modifications from their source.
* Same path from [phpmyadmin/docker](https://github.com/phpmyadmin/docker)
  * `etc/nginx/conf` - Updated the `error_log` to go to stdout. If you're running
    in a container you want to see these logs. This is possibly a concept change
    that would benefit from being changed upstream.
  * `run.sh` - stripped phpmyadmin bits and added phppgadmin ones instead
  * `Dockerfile` - replaced mysql related bits with psql bits

### Inspiration / Major changes
Files in this section are a derative of others where the concept has been taken
 and worked into the target
* `etc/config.inc.php` - Sources
  * [phpmyadmin/docker etc/phpmyadmin/config.inc.php](https://github.com/phpmyadmin/docker/blob/master/etc/phpmyadmin/config.inc.php) -
    Environmental handling into php variables to feed phpPgAdmin config.
    Includes worked idea for handling multiple servers
  * [phppgadmin/phppgadmin conf/config.inc.php-dist](https://github.com/phppgadmin/phppgadmin/blob/master/conf/config.inc.php-dist) -
    Standard phpPgAdmin config file. A drop in replacement for this one had to be created. D'uh!

# Environment variables
## Single PostgreSQL server variables
Defining `PPA_HOST` will put the config into a single server mode
  * `PPA_HOST`      - Address/Hostname of the PostgreSQL server. eg `localhost`
    **No Default**
  * `PPA_DESC`      - Name of the PostgreSQL server in the left panel.
     Defaults to value of `PPA_HOST`
  * `PPA_PORT`      - TCP port of the PostgreSQL. Defaults to `5432`
  * `PPA_SSLMODE`   - Go read
    [pgppgadmin config.inc.php-dist](https://github.com/phppgadmin/phppgadmin/blob/master/conf/config.inc.php-dist)
    Defaults to `allow`
  * `PPA_DEFAULTDB` - Default database to connect to on the server. Defaults to `postgres`

## Multiple PostgreSQL variables
These are for connecting to multiple servers. To use, do NOT define any of `PPA_HOST` as that would put the config into a single server mode.  `PPA_DESC`, `PPA_PORT`, `PPA_SSLMODE` or `PPA_DEFAULTDB` are ignored in multi server mode.
Each variable in this section are a comma separated list.
  * `PPA_HOSTS`
  * `PPA_DESCS`
  * `PPA_PORTS`
  * `PPA_SSLMODES`
  * `PPA_DEFAULTDBS`

Defaults are the same as single server mode. If you want to define any one value
 as non default you MUST define the list to that point.

Eg for a non standard port on the 3rd server (but standard on all others)
 use `PPA_PORTS=5432,5432,1234`

## Other Configurable Variables
See documentation at [pgppgadmin config.inc.php-dist](https://github.com/phppgadmin/phppgadmin/blob/master/conf/config.inc.php-dist)
 for similar named variables. These variables are uppercase and prepended with
 `PPA_` unless stated defaults are as per upstream.
  * `PPA_DEFAULT_LANG`
  * `PPA_AUTOCOMPLETE`
  * `PPA_EXTRA_LOGIN_SECURITY`
  * `PPA_OWNED_ONLY`
  * `PPA_SHOW_COMMENTS`
  * `PPA_SHOW_ADVANCED`
  * `PPA_SHOW_SYSTEM`
  * `PPA_MIN_PASSWORD_LENGTH`
  * `PPA_LEFT_WIDTH`
  * `PPA_THEME`
  * `PPA_SHOW_OIDS`
  * `PPA_MAX_ROWS`
  * `PPA_MAX_CHARS`
  * `PPA_USE_XHTML_STRICT`
  * `PPA_HELP_BASE`
  * `PPA_AJAX_REFRESH`
  * `PPA_PLUGINS`

# Usage etc
This pod binds to port 80 and outputs logs to stdout.
Docker, Kubernetes etc has good documentation. Please feel free to refer to that
 or to create a PR with the sort of documentation you would like.

# Contributions
If you want to make this better, eg by ading tests or better docs. Please feel free
 to create a Pull Request
