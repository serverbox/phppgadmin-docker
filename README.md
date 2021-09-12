# Dockerized build of phpPgAdmin with multi server support

The work was inspired by [phpmyadmin/docker](https://github.com/phpmyadmin/docker)
and it's ability to support multiple database servers.

The initial driver is from an k8s environment on AWS making use of multiple RDS instances.
To provide a phpPgAdmin interface I was forced to roll out the following for each RDS instance
* Ingress
* Pod with containers
As the environment uses helm there is a common chart with a value file for each.

For one or two instances that's fine. For a dozen it's a little tedious.

# Config
There is no environment variable mapping by choice. The intent is for the user to map a custom config by adding `config.inc.php` locally and then starting the container with with `-v path/to/config_folder:/phppgadmin/conf/` or by using a configmap in kubernetes to splice the config in at runtime.

# Usage etc
This pod binds to port 9000 and outputs logs to stdout.
Docker, Kubernetes etc has good documentation. Please feel free to refer to that
 or to create a PR with the sort of documentation you would like.

# Contributions
If you want to make this better, eg by ading tests or better docs. Please feel free
 to create a Pull Request

# Known issues
## AWS / User does not own DB
### Work Around
If you encounter the following. Collapse the DB trees in the left column
 and log in again.

### Issue
**rdsadmin**, not the user that was created is the superuser.
This was encountered when running php7

If you are using this in AWS with RDS instances you may get issues that look
 like login / session persistance bugs. This happens when you log into a database
 and the tree expands trying to access all databases. The **rdsadmin** database is
 inaccessable and so the login credentials are then reset.
 From what I can see this is due to the way AWS creates / manages the PostgreSQL
 instacnes. AWS creates an **rdsadmin** user which then creates the master user.
 This master user is NOT a superuser. You cannot get access to the rdsadmin
 database. As such if you need to collapse the tree on the left and login again.
 You will probably encounter this elsewhere if you use a user which does not have
 **USAGE** on all databases.

