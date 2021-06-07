<?php

it('allows properties to be accessed from the value', function () {
    expect(['foo' => 1])->foo->toBeInt()->toEqual(1);
});

it('can access multiple properties from the value', function () {
    expect(['foo' => 'bar', 'hello' => 'world'])
        ->foo->toBeString()->toEqual('bar')
        ->hello->toBeString()->toEqual('world');
});

it('works with not', function () {
    expect(['foo' => 'bar', 'hello' => 'world'])
        ->foo->not->toEqual('world')->toEqual('bar')
        ->hello->toEqual('world')->not()->toEqual('bar');
});

it('works with each', function () {
    expect(['numbers' => [1,2,3,4], 'words' => ['hey', 'there']])
        ->numbers->toEqual([1,2,3,4])->each->toBeInt->toBeLessThan(5)
        ->words->each(fn ($word) => $word->toBeString())->not->toBeInt();
});

it('can compose complex expectations', function () {
    expect(['foo' => 'bar', 'numbers' => [1,2,3,4]])
        ->toContain('bar')->toBeArray()
        ->numbers->toEqual([1,2,3,4])->not()->toEqual('bar')->each->toBeInt
        ->foo->not->toEqual('world')->toEqual('bar')
        ->numbers->toBeArray();
});

it('works with objects', function () {
    expect((object) ['foo' => 'bar', 'numbers' => [1,2,3,4]])
        ->foo->toEqual('bar')->not->toEqual('world')
        ->numbers->each->toBeInt->tobeLessThan(5);
});
