<?php

return EzSystems\EzPlatformCodeStyle\PhpCsFixer\Config::create()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . '/src')
            ->files()->name('*.php')
    )
;
