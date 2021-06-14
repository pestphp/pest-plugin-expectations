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
     * @var bool
     */
    private $opposite = false;

    /**
     * Creates a new higher order expectation.
     *
     * @param mixed $value
     */
    public function __construct(Expectation $original, $value)
    {
        $this->original    = $original;
        $this->expectation = $this->expect($value);
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
            // @phpstan-ignore-next-line
            return new self($this->original, $this->original->value->$name(...$arguments));
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
            return new self($this->original, $this->retrieve($name, $this->original->value));
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

        $this->opposite = false;

        return $this;
    }
}
