php-tick
========

Simple active record implementation with the least amount of fuss needed to make it work fast.

Support for both SQL and noSQL databases. 
No support for foreign key relations. 

INSTALLATION
------------
  * Copy the Tick folder to your site
  * Add the following lines to you script
  * require_once 'Tick/Tick.php';
  * spl_autoload_register(array('Tick', 'autoload'));

REQUIREMENTS
------------
  * Php version 5.1.2< build with the following extension: pcre, SPL (default build-in in most distributions)
 