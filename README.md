# PHP Loader

[![Latest Release](https://backuptrain.dk/internal-projects/php-loader/-/badges/release.svg)](https://backuptrain.dk/internal-projects/php-loader/-/releases)
[![pipeline status](https://backuptrain.dk/internal-projects/php-loader/badges/master/pipeline.svg)](https://backuptrain.dk/internal-projects/php-loader/-/pipelines)
[![coverage status](https://backuptrain.dk/internal-projects/php-loader/badges/master/coverage.svg)](https://backuptrain.dk/internal-projects/php-loader/-/graphs/master/charts)

A simple PHP File or class loader for PHP. Built with PHP.

## Table of Contents

- [Introduction](#introduction)
- [Getting Started](#getting-started)
    - [Installation](#installation)
- [Dependencies](#dependencies)
    - [symfony/finder](#symfonyfinder)
- [Usage](#usage)
    - [Multiple Paths](#multiple-paths)
- [Filename Constraints](#filename-constraints)
    - [Using Multiple file names](#using-multiple-file-names)
- [Loading Classes](#loading-classes)
    - [Has Method](#has-method)
    - [Call Static](#call-static)
    - [Class or inheritance requirement](#class-or-inheritance-requirement)
    - [Constructing, invoking or calling](#constructing-invoking-or-calling)
- [Optimizations](#optimizations)
    - [Generating cache map](#generating-cache-map)
- [Real life ground-breaking examples for cool kidz!! 😎](#real-life-ground-breaking-examples-for-cool-kidz-)
    - [Loading routes](#loading-routes)
    - [Loading and initializing Hooks](#loading-and-initializing-hooks)
    - [Loading and initializing Blocks](#loading-and-initializing-blocks)
- [Credits](#credits)
- [Testing](#testing)
- [License](#license)

## Introduction

This package is a tool to help you initialize parts of your projects by loading in all files that match certain filename
rules in a defined directory.

You may even initialize the classes contained in these files as long as they are PSR-4 compliant.

For instance, you may use this tool to load all files in a "/routes" directory or all files ending with "Block.php" and
initialize all found classes that extend `Block` and then call `init` on them.

More specifically, this tool is made for, but not dependent on, the WP-Framework. Here it is useful for loading in all
routes, registering all blocks and initializing all Hooks.

## Getting Started

To get started install the package as described below in [Installation](#installation).

To use the tool have a look at [Usage](#usage)

### Installation

Install using composer

```bash
composer require morningtrain/php-loader
```

## Dependencies

### symfony/finder

[Finder](https://symfony.com/doc/current/components/finder.html) is used to find files in the directory

## Usage

First create a Loader using `Loader::create`. This takes an absolute path to the directory you wish to load from as an
argument and returns a Loader. The Loader is further configured by chaining.

In its simples form the Loader only needs a path. This will tell it to load all .php files in that directory
using `require`

```php
    // Loading all PHP files in ./MyDir
    use Morningtrain\PHPLoader\Loader;
    
    Loader::create(__DIR__ . '/MyDir');
```

### Multiple Paths

You may supply an array of full paths to `Loader::create` if you need to handle multiple directories;

```php
    // Loading all PHP files in ./MyDir and ./MyOtherDir
    use Morningtrain\PHPLoader\Loader;
    
    Loader::create([__DIR__ . '/MyDir',__DIR__ . '/MyOtherDir']);
```

## Filename Constraints

To limit the loader to only load files with a given name use `fileName(string|array $filename)`
See [Symfoni Finder: File Name](https://symfony.com/doc/current/components/finder.html#file-name) for options.

By default `$fileName` is `*.php`

```php
    // Loading all PHP files that end with "Foo" in ./MyDir
    use Morningtrain\PHPLoader\Loader;
    
    Loader::create(__DIR__ . '/MyDir')
        ->fileName('*Foo.php');
```

### Using Multiple file names

If you need to allow multiple filename formats then supply an array for `Loader::fileName`

## Loading Classes

As long as no class related options are set on the Loader it will simply load the files.

This is useful for route files and similar.

If you have classes that you wish to load and initialize then read on!

**Note:**
All files will be loaded even if the class requirements are not fulfilled. The Loader has no knowledge of its classes
before they are loaded.

### Has Method

Aborts handling a found class if it does not have a specific method.

**Note:** it is not necessary to specify `hasMethod` if `call` or `callStatic` is used.

```php
    // Loading all PHP files in ./MyDir and invoke them if they have the method myMethod
    use Morningtrain\PHPLoader\Loader;
    
    Loader::create(__DIR__ . '/MyDir')
        ->hasMethod('myMethod')
        ->invoke();
```

### Call Static

To call a static method on all loaded classes specify the method using `Loader::callStatic($methodName)`

This will call said method on every loaded class that has it. You do not need to check using `Loader::hasMethod`
beforehand

```php
    // Loading all PHP files in ./MyDir and call a static method on it if it is of the class Foo
    use Morningtrain\PHPLoader\Loader;
    
    Loader::create(__DIR__ . '/MyDir')
        ->isA(\Foo::class)
        ->callStatic('myMethod');
```

### Class or inheritance requirement

To only call methods on classes that are of a given class or extended from it use `Loader::isA($className)`. This works
the same way is `ia_a($obj,$class)` where $obj is the found class.

If the found class does not match the required class then the Loader will stop handling the current class and the class
will never be constructed or called.

```php
    // Loading all PHP files in ./MyDir and call a static method on it if it is of the class Foo
    use Morningtrain\PHPLoader\Loader;
    
    Loader::create(__DIR__ . '/MyDir')
        ->isA(\Foo::class)
        ->callStatic('myMethod');
```

### Constructing, invoking or calling

You can also construct an instance from the loaded classes, call a method on an instance or invoke an instance using the
Loader.

If you use `Loader::invoke` or `Loader::call` then it is not necessary to use `Loader::construct` as well

```php
    // Loading all PHP files in ./MyDir, construct them and then call 'myMethod'
    use Morningtrain\PHPLoader\Loader;
    
    Loader::create(__DIR__ . '/MyDir')
        ->call('myMethod');
```

## Optimizations

**(WIP 🚧)**

### Generating cache map

## Real life ground-breaking examples for cool kidz!! 😎

This class can be used in many ways, since it is so useful and great. Here are a couple of examples!

### Loading routes

Loading all routes would look like this:

```php
    // Loading all PHP files in ./App/routes
    use Morningtrain\PHPLoader\Loader;
    
    Loader::create(__DIR__ . '/App/routes');
```

### Loading and initializing Hooks

Loading and initializing all hooks would look like this:

```php
    // Loading all PHP files in ./App/Hooks and invoking them
    use Morningtrain\PHPLoader\Loader;
    
    Loader::create(__DIR__ . '/App/Hooks')
        ->isA(\Morningtrain\WP\Core\Abstracts\AbstractHook::class)
        ->invoke();
```

### Loading and initializing Blocks

Loading and initializing all blocks would look like this:

```php
    // Loading all PHP files in ./App/Blocks and initializing them
    use Morningtrain\PHPLoader\Loader;
    
    Loader::create(__DIR__ . '/App/Block')
        ->fileName('*Block.php')
        ->call('init');
```

## Credits

- [Mathias Munk](https://github.com/mrmoeg)
- [All Contributors](../../contributors)

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
