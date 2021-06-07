<?php

it('will not blow up when ray is not installed', function () {
    expect(true)->ray()->toBe(true);
});
