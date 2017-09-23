<?php

namespace NexusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use NexusBundle\Abstracts\GameAbstracts;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Class GameController
 * @package NexusBundle\Controller
 */
class GameController extends GameAbstracts
{

    use \NexusBundle\Helpers\TimeHelpers;

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Return moves of the best strategy to win the game<br/><br/>Excepted results:<br/>strategy => array<br/>duration_ms => integer",
     * )
     * @SWG\Parameter(
     *     name="game_state",
     *     in="query",
     *     description="data containing an even number of (int, float) values representing the initial game state",
     *     type="string",
     *     required=true
     * )
     *
     * @Rest\Get("/best_strategy")
     * @param Request $request
     * @return View
     */
    public function indexAction(Request $request)
    {
        $this->startTime();

        //Parameters from get query
        $gameState   = $request->get('game_state');
        $cards       = explode(',', $gameState);

        $statusCode         = Response::HTTP_BAD_REQUEST;
        $response           = "Error: Number of cards must be even";

        if(empty($cards) || (!ctype_digit(implode('', $cards))))
        {
            $statusCode         = Response::HTTP_BAD_REQUEST;
            $response           = "Error: Invalid input";
        }
        else
        {
            if (!(count($cards)%2))
            {
                $this->strategy($cards);
                $moves = $this->getMoves();

                $statusCode         = Response::HTTP_OK;
                $response = [
                    'strategy'      => $moves,
                    'duration_ms'   => $this->stopTime()
                ];
            }
        }

        return new View($response, $statusCode);
    }
}
