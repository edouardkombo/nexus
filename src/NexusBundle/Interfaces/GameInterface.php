<?php

namespace NexusBundle\Interfaces;

/**
 * Interface GameInterface
 * @package NexusBundle\Interfaces
 */
interface GameInterface
{
    /**
     * Record best Moves
     *
     * @param array $cards Available cards
     * @param array $players Moves of all players
     * @param integer $iterator Which player is playing
     * @return array
     */
     function setMoves($cards, $players, $iterator);

    /**
     * Get the best strategy's moves for player A
     *
     * @return array
     */
    function getMoves();
}