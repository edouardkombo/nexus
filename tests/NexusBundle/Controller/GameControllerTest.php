<?php

namespace NexusBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PHPUnit\Framework\TestCase;

class GameControllerTest extends WebTestCase
{
    /**
     * Test any even valid input
     */
    public function testEvenInput()
    {
        $client = static::createClient();

        $cards = $this->mockedData()['evens'];

        foreach($cards as $k => $v)
        {
            $expectedNumberOfMoves = count($cards[$k])/2;
            $cardsParams = implode(',', $cards[$k]);

            $crawler = $client->request('GET', '/api/best_strategy?game_state='.$cardsParams);

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
            $strategyKeys = (array) $response->strategy;

            //Ensure that response contains two main keys
            $this->assertCount(2, array_keys($responseKeys));

            //Ensure that these keys are "strategy" and "duration_ms"
            $this->assertContains('strategy', array_keys($responseKeys));
            $this->assertContains('duration_ms', array_keys($responseKeys));

            //Ensure that duration_ms is not equal to 0
            $this->assertGreaterThan(0, $response->duration_ms);

            //Ensure that response contains two main keys
            $this->assertCount(2, array_keys($strategyKeys));

            //Ensure that these keys are "direction" and "value"
            $this->assertContains('direction', array_keys($strategyKeys));
            $this->assertContains('value', array_keys($strategyKeys));

            //Ensure that "direction" and "value" contain at least one entry, and equal n cards/2
            $this->assertGreaterThan(0, count($strategyKeys['direction']));
            $this->assertCount($expectedNumberOfMoves, $strategyKeys['direction']);
            $this->assertGreaterThan(0, count($strategyKeys['value']));
            $this->assertCount($expectedNumberOfMoves, $strategyKeys['value']);

            //Ensure that values inside strategy's value match input parameters
            foreach($strategyKeys['value'] as $kk => $vv)
            {
                $this->assertContains($vv, $cards[$k]);
            }
        }
    }

    /**
     *  Test any odd input possible
     */
    public function testOddInput()
    {
        $client = static::createClient();

        $cards = $this->mockedData()['odds'];

        foreach($cards as $k => $v)
        {
            $cardsParams = implode(',', $cards[$k]);

            $crawler = $client->request('GET', '/api/best_strategy?game_state='.$cardsParams);

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
            $this->assertContains("Error: Number of cards must be even", $response);
        }
    }

    /**
     * Test any invalid input possible
     */
    public function testInvalidInput()
    {
        $client = static::createClient();

        $cards = $this->mockedData()['invalids'];

        foreach($cards as $k => $v)
        {
            $cardsParams = implode(',', $cards[$k]);

            $crawler = $client->request('GET', '/api/best_strategy?game_state='.$cardsParams);

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
            $this->assertContains("Error: Invalid input", $response);
        }
    }

    /**
     * Where we define mock data
     *
     * @return array
     */
    private function mockedData()
    {
        $evens = [
            [3,9,10,6,7,8,1,5,2,4],
            [5,5,5,5],
            [4,5,5,3],
            [8,3,1,9,0,4]
        ];

        $odds = [
            [9,5,6],
            [0,5,7,8,4],
            [6,3,1,0,8,4,9]
        ];

        $invalids = [
            [],
            ['abcd_something_happenned'],
            ['a',5,'c',8],
            ['*','$','€'],
            [3,9,10,6,7,8,1,5,2,'*'],
        ];

        return (array) [
            'evens' => $evens,
            'odds' => $odds,
            'invalids' => $invalids
        ];
    }
}