<?php

declare(strict_types=1);

namespace Pest\Expectations;

use BadMethodCallException;

/**
 * @internal
 *
 * @mixin Expectation
 */
class Every
{

    /**
     * @var Expectation
     */
    private $original;

    /**
     * Creates a new iterable expectation.
     */
    public function __construct(Expectation $original)
    {
        $this->original = $original;

        if (!is_iterable($this->original->value)) {
            throw new BadMethodCallException("The `every` call only support iterable types.");
        }
    }

    public function and($value)
    {
        return new Expectation($value);
    }

    public function not()
    {
        return $this->original->not();
    }

    public function __call(string $name, array $arguments)
    {
        foreach ($this->original->value as $item) {
            (new Expectation($item))->$name(...$arguments);
        }

        return $this;
    }

}
