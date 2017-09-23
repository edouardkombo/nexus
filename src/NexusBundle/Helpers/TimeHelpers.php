<?php

namespace NexusBundle\Helpers;

/**
 * Trait TimeHelpers
 * @package NexusBundle\Helpers
 */
trait TimeHelpers
{

    /**
     * @var float
     */
    public $startTime = 0;

    /**
     * @return float
     */
    function startTime()
    {
        return (float) $this->startTime = microtime(true);
    }

    /**
     * Chrono stops
     *
     * @return float
     */
    function stopTime()
    {
        $currentTime = (float) microtime(true);
        return (float) round(($currentTime - $this->startTime) * 1000, 4);

    }
}