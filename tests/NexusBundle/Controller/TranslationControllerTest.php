<?php

namespace NexusBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;

class TranslationControllerTest extends WebTestCase
{
    /**
     * Test any valid sentence
     */
    public function testValidSentence()
    {
        $client = static::createClient();

        $input = $this->mockedData()['valid'];

        foreach($input as $k => $v)
        {
            $sentence                   = array_keys($input[$k])[0];
            $lang                       = $input[$k][$sentence]['lang'];
            $maxChars                   = $input[$k][$sentence]['max_characters'];
            $final_string_translated    = $input[$k][$sentence]['final_string_translated'];
            $final_string               = $input[$k][$sentence]['final_string'];

            $crawler = $client->request('GET', "/api/most_words?sentence=$sentence&output_language=$lang&max_characters=$maxChars");

            //Ensure that http status code is 200
            $this->assertEquals(200, $client->getResponse()->getStatusCode());

            //Ensure that the "Content-Type" header is "application/json"
            $this->assertTrue(
                $client->getResponse()->headers->contains(
                    'Content-Type',
                    'application/json'
                ),
                'The "Content-Type" header is "application/json"'
            );

            $response = json_decode($client->getResponse()->getContent());
            $responseKeys = (array) $response;

            //Ensure that response contains four keys
            $this->assertCount(4, array_keys($responseKeys));

            //Ensure that these keys are "original_sentence", "final_string", "final_string_translated" and "duration_ms"
            $this->assertContains('original_sentence', array_keys($responseKeys));
            $this->assertContains('final_string', array_keys($responseKeys));
            $this->assertContains('final_string_translated', array_keys($responseKeys));
            $this->assertContains('duration_ms', array_keys($responseKeys));

            //Ensure that duration_ms is not equal to 0
            $this->assertGreaterThan(0, $response->duration_ms);

            //Ensure that returned values are expected values
            $this->assertContains($sentence, $response->original_sentence);
            $this->assertContains($final_string_translated, $response->final_string_translated);
            $this->assertContains($final_string, $response->final_string);
        }
    }

    /**
     * Test any invalid input possible
     */
    public function testInvalidInput()
    {
        $client = static::createClient();

        $input = $this->mockedData()['invalid'];

        foreach($input as $k => $v)
        {
            $sentence                   = array_keys($input[$k])[0];
            $lang                       = $input[$k][$sentence]['lang'];
            $maxChars                   = $input[$k][$sentence]['max_characters'];

            $crawler = $client->request('GET', "/api/most_words?sentence=$sentence&output_language=$lang&max_characters=$maxChars");

            //Ensure that http status code is 400
            $this->assertEquals(400, $client->getResponse()->getStatusCode());

            //Ensure that the "Content-Type" header is "application/json"
            $this->assertTrue(
                $client->getResponse()->headers->contains(
                    'Content-Type',
                    'application/json'
                ),
                'The "Content-Type" header is "application/json"'
            );

            $response = json_decode($client->getResponse()->getContent());

            //Ensure that we get the expected response
            $this->assertContains("Error: Invalid request", $response);
        }
    }

    /**
     * Where we define mock data
     *
     * @return array
     */
    private function mockedData()
    {
        $valid = [
            ["Mon nom est Edouard Kombo" =>
                [
                    'lang' => 'en',
                    'final_string' => 'My name is',
                    'final_string_translated' => 'Mon nom est',
                    'max_characters' => 3
                ]
            ],
            ["My hometown seems so far right now" =>
                [
                    'lang' => 'fr',
                    'final_string' => 'Ma ville natale semble si loin',
                    'final_string_translated' => 'My hometown seems so far away',
                    'max_characters' => 6
                ]
            ],
        ];

        $invalid = [
            ["" =>
                [
                    'lang' => 'en',
                    'final_string' => '',
                    'final_string_translated' => '',
                    'max_characters' => 3
                ]
            ],
            ["0123456789" =>
                [
                    'lang' => 'fr',
                    'final_string' => '',
                    'final_string_translated' => '',
                    'max_characters' => 6
                ]
            ],
            ["abcdefghijklmnopqrstuvwxyz" =>
                [
                    'lang' => 'fr',
                    'final_string' => '',
                    'final_string_translated' => '',
                    'max_characters' => 6
                ]
            ],
            ["<?php echo 'test_me';?>" =>
                [
                    'lang' => 'fr',
                    'final_string' => '',
                    'final_string_translated' => '',
                    'max_characters' => 6
                ]
            ],
        ];

        return (array) [
            'valid' => $valid,
            'invalid' => $invalid
        ];
    }
}