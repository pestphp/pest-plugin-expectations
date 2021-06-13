<?php

use PHPUnit\Framework\ExpectationFailedException;

dataset('test array', [
    'test array' => [
        'array' => [
            'a'             => 1,
            'b',
            'c'             => 'world',
            'd'             => [
                'e' => 'hello',
            ],
            'key.with.dots' => false,
        ],
    ],
]);

test('pass', function ($array, $key) {
    expect($array)->toHaveKey($key);
})->with('test array')->with([
    'plain key'           => 'c',
    'nested key'          => 'd.e',
    'plain key with dots' => 'key.with.dots',
]);

test('pass with value', function ($array, $key, $value) {
    expect($array)->toHaveKey($key, $value);
})->with('test array')->with([
    'plain key'           => [
        'key'   => 'c',
        'value' => 'world',
    ],
    'nested key'          => [
        'key'   => 'd.e',
        'value' => 'hello',
    ],
    'plain key with dots' => [
        'key'   => 'key.with.dots',
        'value' => false,
    ],
]);

test('failures', function ($array, $key) {
    expect($array)->toHaveKey($key);
})->with('test array')->with([
    'plain key'           => 'foo',
    'nested key'          => 'd.bar',
    'plain key with dots' => 'missing.key.with.dots',
])->throws(ExpectationFailedException::class);

test('fails with wrong value', function ($array, $key, $value) {
    expect($array)->toHaveKey($key, $value);
})->with('test array')->with([
    'plain key'           => [
        'key'   => 'c',
        'value' => 'bar',
    ],
    'nested key'          => [
        'key'   => 'd.e',
        'value' => 'foo',
    ],
    'plain key with dots' => [
        'key'   => 'key.with.dots',
        'value' => true,
    ],
])->throws(ExpectationFailedException::class);

test('not failures', function ($array, $key) {
    expect($array)->not->toHaveKey($key);
})->with('test array')->with([
    'plain key'           => 'c',
    'nested key'          => 'd.e',
    'plain key with dots' => 'key.with.dots',
])->throws(ExpectationFailedException::class);

test('not failures with correct value', function ($array, $key, $value) {
    expect($array)->not->toHaveKey($key, $value);
})->with('test array')->with([
    'plain key'           => [
        'key'   => 'c',
        'value' => 'world',
    ],
    'nested key'          => [
        'key'   => 'd.e',
        'value' => 'hello',
    ],
    'plain key with dots' => [
        'key'   => 'key.with.dots',
        'value' => false,
    ],
])->throws(ExpectationFailedException::class);
