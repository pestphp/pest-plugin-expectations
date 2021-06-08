<?php

declare(strict_types=1);

namespace Pest\Expectations;

use Pest\Expectations\Concerns\Expectations;

/**
 * @internal
 *
 * @mixin Expectation
 */
final class HigherOrderExpectation
{
    use Expectations;

    /**
     * @var Expectation
     */
    private $original;

    /**
     * @var Expectation|Each
     */
    private $expectation;

    /**
     * @var bool
     */
    private $opposite = false;

    /**
     * @var string
     */
    private $name;

    /**
     * Creates a new higher order expectation.
     */
    public function __construct(Expectation $original, string $name)
    {
        $this->original = $original;
        $this->name = $name;
    }

    /**
     * Create a new higher order expectation for a property.
     */
    public static function forProperty(Expectation $original, string $property): HigherOrderExpectation
    {
        $instance = new static($original, $property);
        $instance->expectation = $instance->expect($instance->getPropertyValue());

        return $instance;
    }

    /**
     * Retrieves the property value from the original expectation.
     */
    private function getPropertyValue()
    {
        if (is_array($this->original->value)) {
            return $this->original->value[$this->name];
        }

        if (is_object($this->original->value)) {
            return $this->original->value->{$this->name};
        }
    }

    /**
     * Create a new higher order expectation for a method call.
     */
    public static function forMethod(Expectation $original, string $method, ...$arguments): HigherOrderExpectation
    {
        $instance = new static($original, $method);
        $instance->expectation = $instance->expect($instance->getMethodValue(...$arguments));

        return $instance;
    }

    /**
     * Retrieves the value of the method from the original expectation.
     */
    private function getMethodValue(...$arguments)
    {
        return $this->original->value->{$this->name}(...$arguments);
    }

    /**
     * Creates the opposite expectation for the value.
     */
    public function not(): HigherOrderExpectation
    {
        $this->opposite = !$this->opposite;

        return $this;
    }

    /**
     * Dynamically calls methods on the class with the given arguments on each item.
     *
     * @param array<int|string, mixed> $arguments
     */
    public function __call(string $name, array $arguments): HigherOrderExpectation
    {
        if (!$this->originalHasMethod($name)) {
            return static::forMethod($this->original, $name, ...$arguments);
        }

        return $this->performAssertion($name, ...$arguments);
    }

    /**
     * Accesses properties in the value or in the expectation.
     */
    public function __get(string $name): HigherOrderExpectation
    {
        if ($name == 'not') {
            return $this->not();
        }

        if (!$this->originalHasMethod($name)) {
            return static::forProperty($this->original, $name);
        }

        return $this->performAssertion($name);
    }

    /**
     * Determines if the original expectation has the given method name.
     */
    private function originalHasMethod($name): bool
    {
        return method_exists($this->original, $name) || $this->original::hasExtend($name);
    }

    /**
     * Performs the given assertion with the current expectation.
     */
    private function performAssertion($name, ...$arguments): HigherOrderExpectation
    {
        $this->expectation = $this->opposite
            ? $this->expectation->not()->{$name}(...$arguments)
            : $this->expectation->{$name}(...$arguments);

        $this->opposite = false;

        return $this;
    }
}
