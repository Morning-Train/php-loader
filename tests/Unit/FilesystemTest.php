<?php

    it('has test app directory', function () {
        expect(is_dir(dirname(__DIR__) . '/TestApp'))->toBeTrue();
    });