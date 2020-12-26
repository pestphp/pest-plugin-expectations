<?php

use PHPUnit\Framework\ExpectationFailedException;

beforeEach(function () {
    $this->user = [
        'id'    => 1,
        'name'  => 'Nuno',
        'email' => 'enunomaduro@gmail.com',
    ];
});

test('pass', function () {
    expect(json_encode($this->user))->toBeJson();
    expect($this->user)->not->toBeJson();
    expect($this->user['name'])->not->toBeJson();
});

test('failures', function () {
    expect($this->user['name'])->toBeJson();
})->throws(ExpectationFailedException::class);

test('not failures', function () {
    expect(json_encode($this->user))->not->toBeJson();
})->throws(ExpectationFailedException::class);
