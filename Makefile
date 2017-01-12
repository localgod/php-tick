.PHONY: tools autofix build doc

autofix:
	find . -name '*.php'  -path ./vendor -prune | xargs dos2unix
	find . -name '*.sh'   -path ./vendor -prune | xargs dos2unix
	find . -name '*.json' -path ./vendor -prune | xargs dos2unix
	find . -name '*.md'   -path ./vendor -prune | xargs dos2unix
	find . -name '*.xml'  -path ./vendor -prune | xargs dos2unix
	find . -name '*.php'  -path ./vendor -prune | xargs expand
	find . -name '*.sh'   -path ./vendor -prune | xargs expand
	find . -name '*.json' -path ./vendor -prune | xargs expand
	find . -name '*.md'   -path ./vendor -prune | xargs expand
	find . -name '*.xml'  -path ./vendor -prune | xargs expand
	php phpcbf.phar
	php phpcs.phar
	php php-cs-fixer.phar fix ./src

setup:
	php composer.phar self-update
	php composer.phar update

clean:
	if [ -e ./build ]; then rm -rf ./build ; fi
	if [ -e ./dist ]; then rm -rf ./dist ; fi
	if [ -e ./docs ]; then rm -rf ./docs ; fi

tools:
	if [ ! -e phpmd.phar ];        then wget -O ./phpmd.phar --no-check-certificate http://static.phpmd.org/php/latest/phpmd.phar; fi
	if [ ! -e phploc.phar ];       then wget -O ./phploc.phar --no-check-certificate https://phar.phpunit.de/phploc.phar; fi
	if [ ! -e phpdox.phar ];       then wget -O ./phpdox.phar --no-check-certificate http://phpdox.de/releases/phpdox.phar; fi
	if [ ! -e composer.phar ];     then wget -O ./composer.phar --no-check-certificate https://getcomposer.org/download/1.3.0/composer.phar; fi
	if [ ! -e phpcs.phar ];        then wget -O ./phpcs.phar --no-check-certificate https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar; fi
	if [ ! -e phpcbf.phar ];       then wget -O ./phpcbf.phar --no-check-certificate https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar; fi
	if [ ! -e phpunit.phar ];      then wget -O ./phpunit.phar --no-check-certificate https://phar.phpunit.de/phpunit.phar; fi
	if [ ! -e php-cs-fixer.phar ]; then wget -O ./php-cs-fixer.phar --no-check-certificate https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v2.0.0/php-cs-fixer.phar; fi
	chmod 755 ./*.phar

build:
	if [ -e ./build ]; then rm -rf ./build ; fi
	rm -rf ./build
	./phpcs.phar
	./phpunit.phar --testdox -c ./phpunit.xml.dist
	-./phpmd.phar ./src text ./pmd.xml

doc: build
	if [ -e ./docs ]; then rm -rf ./docs ; fi
	-./phpmd.phar ./src xml ./pmd.xml  > ./build/phpmd.xml
	./phpcs.phar --report=xml --report-file=./build/phpcs.xml
	./phploc.phar --log-xml=./build/phploc.xml .
	./phpdox.phar

default: autofix
