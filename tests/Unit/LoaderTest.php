<?php

beforeEach(function () {
    if (class_exists('TestApp\Classes\Bar')) {
        \TestApp\Classes\Bar::reset();
    }
    if (class_exists('TestApp\Classes\Foo')) {
        \TestApp\Classes\Foo::reset();
    }
    if (class_exists('TestApp\Classes\FooTwo')) {
        \TestApp\Classes\FooTwo::reset();
    }
});

it('throws if dir does not exist', function () {
    expect(function () {
        \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/schrodingersDirectory");
    })->toThrow(Symfony\Component\Finder\Exception\DirectoryNotFoundException::class);
});

it('can find all files', function () {
    $loader = \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/files");
    expect($loader)->findFiles()->toHaveCount(3);
});

it('can find files by name', function () {
    $loader = \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/files")
        ->fileName('file1.php');
    expect($loader)->findFiles()->toHaveCount(1);
});

it('can exclude files by name', function () {
    $loader = \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/files")
        ->notFileName('file1.php');
    expect($loader)->findFiles()->toHaveCount(2);
});

it('can find files by names', function () {
    $loader = \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/files")
        ->fileName(['file1.php', 'file2.php']);
    expect($loader)->findFiles()->toHaveCount(2);
});

it('can exclude files by names', function () {
    $loader = \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/files")
        ->notFileName(['file1.php', 'file2.php']);
    expect($loader)->findFiles()->toHaveCount(1);
});

it('returns an empty array if no files are found', function () {
    $loader = \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/files")
        ->fileName('404.php');
    expect($loader)->findFiles()->toHaveCount(0);
});

it('loads files', function () {
    \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/files");
    expect(function_exists('testFile1Function'))->toBeTrue();
});

it('can call static method on loaded class', function () {
    if (class_exists('TestApp\Classes\Foo')) {
        expect(TestApp\Classes\Foo::$init)->toBeFalse();
    }
    \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/Classes")
        ->fileName('Foo.php')
        ->callStatic('init');

    expect(TestApp\Classes\Foo::$init)->toBeTrue();
});

it('respects classname', function () {
    if (class_exists('TestApp\Classes\Foo')) {
        expect(TestApp\Classes\Foo::$init)->toBeFalse();
    }
    if (class_exists('TestApp\Classes\Bar')) {
        expect(TestApp\Classes\Bar::$init)->toBeFalse();
    }

    \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/Classes")
        ->isA(\TestApp\Classes\Foo::class)
        ->callStatic('init');

    expect(TestApp\Classes\Foo::$init)->toBeTrue();
    expect(TestApp\Classes\Bar::$init)->toBeFalse();
});

it('respects hasMethod', function () {
    if (class_exists('TestApp\Classes\Bar')) {
        expect(TestApp\Classes\Bar::$init)->toBeFalse();
    }
    if (class_exists('TestApp\Classes\FooTwo')) {
        expect(TestApp\Classes\FooTwo::$init)->toBeFalse();
    }

    \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/Classes")
        ->hasMethod('fooTwoMethod')
        ->callStatic('init');

    expect(TestApp\Classes\Bar::$init)->toBeFalse();
    expect(TestApp\Classes\FooTwo::$init)->toBeTrue();
});

it('can construct', function () {
    if (class_exists('TestApp\Classes\Bar')) {
        expect(TestApp\Classes\Bar::$constructed)->toBeFalse();
    }

    \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/Classes")
        ->isA(\TestApp\Classes\Bar::class)
        ->construct();

    expect(TestApp\Classes\Bar::$constructed)->toBeTrue();
});

it('can invoke', function () {
    if (class_exists('TestApp\Classes\Bar')) {
        expect(TestApp\Classes\Bar::$invoked)->toBeFalse();
    }

    \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/Classes")
        ->isA(\TestApp\Classes\Bar::class)
        ->invoke();

    expect(TestApp\Classes\Bar::$invoked)->toBeTrue();
});

it('can call', function () {
    if (class_exists('TestApp\Classes\Bar')) {
        expect(TestApp\Classes\Bar::$called)->toBeFalse();
    }

    \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/Classes")
        ->isA(\TestApp\Classes\Bar::class)
        ->call('call');

    expect(TestApp\Classes\Bar::$called)->toBeTrue();
});
