# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [2.0.1] - 2019-07-05
### Fixed

-  	Fix domain denormalizer construct signature 

## [2.0.0] - 2019-07-05
### Changed

- support for Symfony 4.3 and 5.0

### Fixed

- Denormalizer updates for v2.0 #23
- The EventDispatcher should be final #4

## [1.5.0] - 2019-03-29
### Added

- Add support for static factories in domain model instantiator

### Changed

- Use new Symfony 4.2

## [1.4.4] - 2018-11-08
### Added

- Added possibility to autowire domain dispatcher

## [1.4.3] - 2018-08-07
### Added

- Implementation of 2 domain rule interfaces is now working #39
- Add information about event timing in itself #40

### Fixed

- Fix wrong listener on doctrine connection #37

## [1.4.2] - 2018-07-18
### Fixed 

- Fix for execution order in post persist rules

## [1.4.1] - 2018-04-10
### Fixed 

- Correct an error that occurs when we flush in a rule.

## [1.4.0] - 2018-04-05
### Added

- Support for embbeded events in the `DomainEvent`

## [1.3.0] - 2018-03-14
### Added

- New method `instantiateWithArguments` on the instantiator (because most part of the time you want to add arguments)
- The deserializer is now only one class: simpler and better. It decorates the serializer the right way.

### Changed

- DEPRECATED: as a result of refactoring the deserializer the `ApiPlatformDomainDeserializer` is deprecated
    to be removed in the 2.x version.

## [1.2.0] - 2018-02-28
### Added

- The domain event dispatcher now supports "delayed" rules that will be execute only after the model is persist.
- Automatic persist detection for doctrine with the new doctrine subscriber `DoctrinePostPersistListener`
- Integration of the doctrine subscriber in the bundle
- The component now uses an interface and a trait to avoid to force you to extends from a specific class

### Changed

- [Minor BC Break] The DomainEventDispatcher now accept a `RuleInterface` instead of `DomainRuleInterface`.
  This is a problem only if you extends the domain dispatcher (which is to do only in very special cases).

## [1.1.0] - 2018-02-09
### Added

- Support for many entity manager

### Fixed

- Doctrine bundle filters configuration were broken by our configurator. This is fixed by #2.

## [1.0.0] - 2018-01-18
### Added

- Domain event support
- Symfony bundle
- Support for ApiPlatform
