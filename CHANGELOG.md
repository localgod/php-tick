# Change Log

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](http://semver.org/).

This project adheres to [Keep a CHANGELOG](http://keepachangelog.com/)

## [Unreleased]

### Added

- Makefile
- phpdox support

### Fixed

- Unit test date error

## [1.1.0] - 2015-04-06

### Added

- Added support for composer
- Much improved query functionality.
- Support for multiple connections (Thanks to briandemant).
- Generalized arguments for TickManager to better support Mongo connection scheme
- Added autoloading for models
- Added a connection manager, simplifying model setup considerably.
- Added toArray() method.
- Better querying options and more general "primary key" implementation

### Changed

- Switched to PSR-4 autoloading

### Fixed

- Fixed bug with storing float values
- Fixed small problem with handling float values.
- Fixed word boundaries errors in regxp.

## [1.0.0] - 2011-04-09

### Added

 - First release.
