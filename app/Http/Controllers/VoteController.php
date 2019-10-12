<?php

namespace App\Http\Controllers;

use App\Game;
use App\Pick;
use App\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{

    public function vote(Request $request)
    {

        $game = Game::first();

        Vote::create([
            'game_id' => $game->id,
            'team' => $request->team,
            'col' => $request->col,
            'row' => $request->row,
            'turn' => $game->turn_number
        ]);

        event(new Vote([$request->col, $request->row, $request->team]));


    }

}
