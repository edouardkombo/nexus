<?php

namespace NexusBundle\Abstracts;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use NexusBundle\Interfaces\GameInterface;


/**
 * Class GameAbstracts
 * @package NexusBundle\Abstracts
 */
abstract class GameAbstracts extends FOSRestController  implements GameInterface
{
    /**
     * @var array
     */
    public $moves = ['direction' => [], 'value' => []];

    /**
     * Best strategy for player A
     *
     * @param array $cards Input
     * @return array
     */
    protected function strategy($cards)
    {
        $players = ['a' => ['direction' => [], 'value' => [], 'weight' => 0], 'b' => ['direction' => [], 'value' => [], 'weight' => 0]];
        $iterator = -1;

        foreach($cards as $k => $v)
        {
            ++$iterator;

            $logic = $this->setMoves($cards, $players, $iterator);
            $players = $logic[0];
            $cards = $logic[1];
        }

        if (empty($cards))
        {
            $players['a']['weight'] = array_sum($players['a']['value']);
            $players['b']['weight'] = array_sum($players['b']['value']);

            //In this example, the winning strategy is the strategy with highest weight
            //No matter who is winning between player A or B, we just choose the best strategy
            $this->moves = ($players['a']['weight'] >= $players['b']['weight']) ? $players['a'] : $players['b'];
            unset($this->moves['weight']);
        }

        return $this->moves;
    }

    /**
     * Record best Moves
     *
     * @param array $cards Available cards
     * @param array $players Moves of all players
     * @param integer $iterator Which player is playing
     * @return array
     */
    function setMoves($cards, $players, $iterator)
    {
        //If iterator is even, it's player A, otherwise it's player B
        if (!($iterator%2))
        {
            if ($cards[0] > end($cards))
            {
                $players['a']['direction'][] = 'left';
                $players['a']['value'][] = $cards[0];
                unset($cards[0]);
            }
            else
            {
                $players['a']['direction'][] = 'right';
                $players['a']['value'][] = end($cards);
                unset($cards[count($cards)-1]);
            }
        }
        else
        {
            if ($cards[0] > end($cards))
            {
                $players['b']['direction'][] = 'left';
                $players['b']['value'][] = $cards[0];
                unset($cards[0]);
            }
            else
            {
                $players['b']['direction'][] = 'right';
                $players['b']['value'][] = end($cards);
                unset($cards[count($cards)-1]);
            }
        }

        $cards = array_values($cards); //Reset array indexes

        return [$players, $cards];
    }

    /**
     * Get the best strategy's moves for player A
     *
     * @return array
     */
    function getMoves()
    {
        return (array) $this->moves;
    }
}