<?php


namespace App\Http\Controllers;


use App\Game;
use App\Pick;
use Illuminate\Http\Request;

class GameController  extends Controller
{

    public function play($team)
    {
        return view('play')->withTeam($team);
    }

    public function update()
    {
        return Game::first();
    }

    public function pick(Request $request)
    {
        $game = $this->update();

        Pick::create([
            'game_id' => $game->id,
            'team' => $request->team,
            'col' => $request->col,
            'row' => $request->row,
            'turn' => $game->turn_number
        ]);

        ++$game->turn_number;
        return response(['status' => $game->save()]);

    }
}
