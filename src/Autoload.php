<?php

declare(strict_types=1);

use Pest\Expectations\Concerns\Expectations;
use Pest\Expectations\Expectation;
use Pest\Expectations\Support\Extendable;

if (class_exists(Pest\Plugin::class)) {
    Pest\Plugin::uses(Expectations::class);
}

/**
 * Creates a new expectation.
 *
 * @param mixed $value the Value
 *
 * @return Expectation|Extendable
 */
function expect($value = null)
{
    if (func_num_args() === 0) {
        return new Extendable(Expectation::class);
    }

    return new Expectation($value);
}
