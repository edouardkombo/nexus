<?php

namespace NexusBundle\Abstracts;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use NexusBundle\Interfaces\TranslationInterface;


/**
 * Class TranslationAbstracts
 * @package NexusBundle\Abstracts
 */
abstract class TranslationAbstracts extends FOSRestController implements TranslationInterface
{

    /**
     * @param string $sentence
     * @param string $lang
     * @param string $apiKey
     * @return array
     */
    function translate($sentence, $lang, $apiKey)
    {
        $client   = $this->get('guzzle.client.google_translate');
        $response = $client->get('', [
            'headers'   => [
                'content-type' => 'application/json',
                'Accept' => 'application/json'],
            'query'     => [
                'q'         => $sentence,
                'target'    => $lang,
                'key'       => $apiKey,
            ]
        ]);

        $statusCode = $response->getStatusCode();
        $content = json_decode($response->getBody(), true);

        return (array) ['statusCode' => $statusCode, 'response' => $content];
    }

    /**
     * @param string $sentence
     * @param integer $maxChars
     * @param string $ellipsis
     * @return string
     */
    function truncate($sentence, $maxChars, $ellipsis = '')
    {
        $sentenceInArray = explode(' ', $sentence);

        if (count($sentenceInArray) > $maxChars && $maxChars > 0)
        {
            $sentence = implode(' ', array_slice($sentenceInArray, 0, $maxChars)).$ellipsis;
        }

        return (string) $sentence;
    }
}