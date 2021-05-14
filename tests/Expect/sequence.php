<?php

test('an exception is thrown if the the type is not iterable', function () {
    expect('Foobar')->sequence();
})->throws(BadMethodCallException::class, 'Expectation value is not traversable.');

test('allows for sequences of checks to be run on traversable data', function () {
    expect([1, 2, 3])
        ->sequence(
            function($expectation) { $expectation->toBeInt()->toEqual(1); },
            function($expectation) { $expectation->toBeInt()->toEqual(2); },
            function($expectation) { $expectation->toBeInt()->toEqual(3); },
        );

    expect(static::getCount())->toBe(6);
});

test('loops back to the start if it runs out of sequence items', function() {
    expect([1, 2, 3, 1, 2, 3, 1, 2])
        ->sequence(
            function($expectation) { $expectation->toBeInt()->toEqual(1); },
            function($expectation) { $expectation->toBeInt()->toEqual(2); },
            function($expectation) { $expectation->toBeInt()->toEqual(3); },
        );

    expect(static::getCount())->toBe(16);
});

test('it works if the number of items in the traversable is smaller than the number of expectations', function () {
    expect([1, 2])
        ->sequence(
            function($expectation) { $expectation->toBeInt()->toEqual(1); },
            function($expectation) { $expectation->toBeInt()->toEqual(2); },
            function($expectation) { $expectation->toBeInt()->toEqual(3); },
        );

    expect(static::getCount())->toBe(4);
});

