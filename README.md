php-tick
========

Simple active record implementation with the least amount of fuss needed to make it work fast.

Support for both SQL and noSQL databases.
No support for foreign key relations.

INSTALLATION
------------
  * Copy the Tick folder to your site
  * `require_once 'autoload.php';`
  * ...Or add the following lines to you script:

		<?php
		require_once 'Tick/Tick.php';
		spl_autoload_register(array('Tick', 'autoload'));
		?>

REQUIREMENTS
------------
  * Php version 5.1.2< build with the following extension: pcre, SPL (default build-in in most distributions)
  * The SOLR storage engine requires the PECL SOLR extension.
