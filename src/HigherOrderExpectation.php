<?php

declare(strict_types=1);

namespace Pest\Expectations;

use Pest\Expectations\Concerns\Expectations;
use Pest\Expectations\Concerns\RetrievesValues;

/**
 * @internal
 *
 * @mixin Expectation
 */
final class HigherOrderExpectation
{
    use Expectations;
    use RetrievesValues;

    /**
     * @var Expectation
     */
    private $original;

    /**
     * @var Expectation|Each
     */
    private $expectation;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var mixed
     */
    private $initialValue;

    /**
     * @var bool
     */
    private $opposite = false;

    /**
     * @var bool
     */
    private $lastCallWasAssertion = false;

    /**
     * Creates a new higher order expectation.
     *
     * @param mixed $value
     * @param mixed $initialValue
     *
     * @phpstan-ignore-next-line
     */
    public function __construct(Expectation $original, $value, $initialValue = null)
    {
        $this->original     = $original;
        $this->expectation  = $this->expect($value);
        $this->value        = $value;
        $this->initialValue = $initialValue ?? $original->value;
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
     * Dynamically calls methods on the class with the given arguments.
     *
     * @param array<int|string, mixed> $arguments
     */
    public function __call(string $name, array $arguments): self
    {
        if (!$this->originalHasMethod($name)) {
            return new self(
                $this->original,
                /* @phpstan-ignore-next-line */
                ($this->lastCallWasAssertion ? $this->initialValue : $this->value)->$name(...$arguments),
                $this->initialValue
            );
        }

        return $this->performAssertion($name, $arguments);
    }

    /**
     * Accesses properties in the value or in the expectation.
     */
    public function __get(string $name): self
    {
        if ($name === 'not') {
            return $this->not();
        }

        if (!$this->originalHasMethod($name)) {
            return new self(
                $this->original,
                $this->retrieve($name, $this->lastCallWasAssertion ? $this->initialValue : $this->value),
                $this->initialValue
            );
        }

        return $this->performAssertion($name, []);
    }

    /**
     * Determines if the original expectation has the given method name.
     */
    private function originalHasMethod(string $name): bool
    {
        return method_exists($this->original, $name) || $this->original::hasExtend($name);
    }

    /**
     * Performs the given assertion with the current expectation.
     *
     * @param array<int|string, mixed> $arguments
     */
    private function performAssertion(string $name, array $arguments): self
    {
        $expectation = $this->opposite
            ? $this->expectation->not()
            : $this->expectation;

        $this->expectation = $expectation->{$name}(...$arguments); // @phpstan-ignore-line

        $this->lastCallWasAssertion = true;
        $this->opposite             = false;

        return $this;
    }
}
