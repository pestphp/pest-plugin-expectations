<?php

test('an exception is thrown if the the type is not iterable', function() {
    $this->expectException(BadMethodCallException::class);

    expect('Foobar')
        ->every()
        ->toEqual('Foobar');
});

test('it maps over each iterable', function () {
    expect([1, 1, 1])
        ->every()
        ->toEqual(1);
});

test('it accepts chained expectations', function() {
    expect([1, 1, 1])
        ->every()
        ->toBeInt()
        ->toEqual(1);
});

test('it works with the not operator', function() {
    expect([1, 2, 3])
        ->every()
        ->not()->toEqual(4);
});

test('it works with the and operator', function() {
    expect([1, 2, 3])
        ->every()
        ->not()->toEqual(4)
        ->and([4, 5, 6])
        ->every()
        ->toBeLessThan(7)
        ->toBeGreaterThan(3)
        ->and('Hello World')
        ->toBeString()
        ->toEqual('Hello World');
});

test('it can be called as a higher order function', function () {
    expect([1, 1, 1])
        ->every->toEqual(1);
});
