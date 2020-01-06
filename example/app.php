<?php

declare(strict_types = 1);

use Chartisan\PHP\Chartisan;

/**
 * Outputing JSON encoded data.
 */
header('Content-Type: application/json');

/**
 * Main application entry point.
 */
return fn (): string => Chartisan::build()
    ->labels(['a', 'b', 'c'])
    ->dataset('Sample 1', [1, 2 ,3])
    ->dataset('Sample 2', [3, 2 ,1])
    ->toJSON();
