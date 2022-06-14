<?php

it('can find files', function () {
    $loader = \Morningtrain\PHPLoader\Loader::create(dirname(__DIR__) . "/TestApp/Classes");
    expect($loader)->findFiles()->toHaveCount(1);
});
