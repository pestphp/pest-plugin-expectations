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
    private $opposite;

    /**
     * @var string
     */
    private $name;

    /**
     * Creates a new higher order expectation.
     */
    public function __construct(Expectation $original, string $name, bool $asMethod = false, ...$arguments)
    {
        $this->original = $original;
        $this->name = $name;
        $this->expectation = $this->generateInitialExpectation($asMethod, ...$arguments);
    }

    /**
     * Generates the initial state of the expectation.
     */
    private function generateInitialExpectation(bool $asMethod, ...$arguments)
    {
        return $this->expect($asMethod ? $this->getMethodValue(...$arguments) : $this->getPropertyValue());
    }

    /**
     * Retrieves the value of the method from the original expectation.
     */
    private function getMethodValue(...$arguments)
    {
        return $this->original->value->{$this->name}(...$arguments);
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
     * Creates the opposite expectation for the value.
     */
    public function not(): HigherOrderExpectation
    {
        $this->opposite = true;

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
            return new static($this->original, $name, true, ...$arguments);
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
            return new static($this->original, $name);
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
    private function performAssertion($name, ...$arguments)
    {
        $this->expectation = $this->opposite
            ? $this->expectation->not()->{$name}(...$arguments)
            : $this->expectation->{$name}(...$arguments);

        $this->opposite = false;

        return $this;
    }
}
