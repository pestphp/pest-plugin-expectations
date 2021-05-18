<?php

declare(strict_types=1);

namespace Pest\Expectations;

use BadMethodCallException;

/**
 * @internal
 *
 * @mixin Expectation
 */
final class Each
{
    /**
     * @var Expectation
     */
    private $original;

    /**
     * @var bool
     */
    private $opposite = false;

    /**
     * Creates an expectation on each item of the iterable "value".
     */
    public function __construct(Expectation $original)
    {
        $this->original = $original;
    }

    /**
     * Allows you to specify a sequential set of
     * expectations for each item in a
     * traversable "value".
     *
     * @param callable ...$expectations
     */
    public function sequence(...$expectations): Each
    {
        if (!is_iterable($this->original->value)) {
            throw new BadMethodCallException('Expectation value is not traversable.');
        }

        $expectationIndex = 0;

        /* @phpstan-ignore-next-line */
        while (count($expectations) < count($this->original->value)) {
            $expectations[] = $expectations[$expectationIndex];
            /* @phpstan-ignore-next-line */
            $expectationIndex = $expectationIndex < count($this->original->value) - 1 ? $expectationIndex + 1 : 0;
        }

        foreach ($this->original->value as $index => $item) {
            call_user_func($expectations[$index], expect($item));
        }

        return $this;
    }

    /**
     * Creates a new expectation.
     *
     * @param mixed $value
     */
    public function and($value): Expectation
    {
        return $this->original->and($value);
    }

    /**
     * Creates the opposite expectation for the value.
     */
    public function not(): Each
    {
        $this->opposite = true;

        return $this;
    }

    /**
     * Dynamically calls methods on the class with the given arguments on each item.
     *
     * @param array<int|string, mixed> $arguments
     */
    public function __call(string $name, array $arguments): Each
    {
        foreach ($this->original->value as $item) {
            /* @phpstan-ignore-next-line */
            $this->opposite ? expect($item)->not()->$name(...$arguments) : expect($item)->$name(...$arguments);
        }

        $this->opposite = false;

        return $this;
    }

    /**
     * Dynamically calls methods on the class without any arguments on each item.
     */
    public function __get(string $name): Each
    {
        /* @phpstan-ignore-next-line */
        return $this->$name();
    }
}
