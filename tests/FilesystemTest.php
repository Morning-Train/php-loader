<?php

    it('has test app directory', function () {
        expect(is_dir(__DIR__ . '/TestApp'))->toBeTrue();
    });