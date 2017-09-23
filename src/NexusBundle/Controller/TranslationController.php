<?php

namespace NexusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use NexusBundle\Abstracts\TranslationAbstracts;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Class TranslationController
 * @package NexusBundle\Controller
 */
class TranslationController extends TranslationAbstracts
{

    use \NexusBundle\Helpers\TimeHelpers;

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Return truncated response of the original sentence's translation<br/><br/>Excepted results:<br/>original_sentence => string<br/>final_string => string<br/>final_string_translated => string<br/>duration_ms => string",
     * )
     * @SWG\Parameter(
     *     name="sentence",
     *     in="query",
     *     description="Sentence to translate",
     *     type="string",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="output_language",
     *     in="query",
     *     description="Translation language",
     *     type="string",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="max_characters",
     *     in="query",
     *     description="Maximum characters allowed for final sentence",
     *     type="string",
     *     required=true
     * )
     *
     * @Rest\Get("/most_words")
     * @param Request $request
     * @return View
     */
    public function indexAction(Request $request)
    {
        $this->startTime();

        //Parameters from get query
        $originalSentence   = $request->get('sentence');
        $outputLanguage     = $request->get('output_language');
        $maxChars           = $request->get('max_characters');

        $statusCode         = Response::HTTP_OK;
        $response           = "Error: Invalid request";

        $apiKey             = $this->getParameter('nexus.google_translate_api_key');

        $translation        = $this->translate($originalSentence, $outputLanguage, $apiKey);
        $_finalString       = $translation['response']['data']['translations'][0]['translatedText'];
        $originalLanguage   = $translation['response']['data']['translations'][0]['detectedSourceLanguage'];
        $finalString        = $this->truncate($_finalString, $maxChars, '');

        if ($translation['statusCode'] === Response::HTTP_OK)
        {
            $newTranslation         = $this->translate($finalString, $originalLanguage, $apiKey);
            $finalStringTranslated  = $newTranslation['response']['data']['translations'][0]['translatedText'];

            if ($newTranslation['statusCode'] === Response::HTTP_OK)
            {

                if (!empty($originalSentence) || !empty($finalString) || !empty($finalStringTranslated))
                {
                    if ($originalSentence === $finalString && $originalSentence === $finalStringTranslated && $finalString === $finalStringTranslated)
                    {
                        $statusCode         = Response::HTTP_BAD_REQUEST;
                    }
                    else
                    {
                        $response = [
                            'original_sentence'         => $originalSentence,
                            'final_string'              => $finalString,
                            'final_string_translated'   => $finalStringTranslated,
                            'duration_ms'               => $this->stopTime(),
                        ];
                    }
                }
                else
                {
                    $statusCode         = Response::HTTP_BAD_REQUEST;
                }
            }
            else
            {
                $statusCode = Response::HTTP_BAD_REQUEST;
            }
        }
        else
        {
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        return new View($response, $statusCode);
    }
}
