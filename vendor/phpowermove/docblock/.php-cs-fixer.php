<?php declare(strict_types=1);

$config = new phootwork\fixer\Config();
$config->getFinder()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return $config;
