<?php

namespace NexusBundle\Interfaces;

/**
 * Interface TranslationInterface
 * @package NexusBundle\Interfaces
 */
interface TranslationInterface
{
    /**
     * @param string $sentence
     * @param string $lang
     * @param string $apiKey
     * @return array
     */
    function translate($sentence, $lang, $apiKey);

    /**
     * @param string $sentence
     * @param integer $maxChars
     * @param string $ellipsis
     * @return string
     */
    function truncate($sentence, $maxChars, $ellipsis);
}