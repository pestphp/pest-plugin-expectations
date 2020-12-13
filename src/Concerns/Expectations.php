<?php

declare(strict_types=1);

namespace Pest\Expectations\Concerns;

use Pest\Expectations\Expectation;

/**
 * @internal
 */
trait Expectations
{
    /**
     * Creates a new expectation.
     *
     * @param mixed $value
     */
    public function expect($value): Expectation
    {
        return new Expectation($value);
    }
}
