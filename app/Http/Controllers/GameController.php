<?php


namespace App\Http\Controllers;


use App\Events\Draw;
use App\Events\NextTurn;
use App\Events\Win;
use App\Game;
use App\Pick;
use Illuminate\Http\Request;

class GameController extends Controller
{

    private $grid_size = 3;
    private $win_lines = [];

    public function play($team)
    {
        return view('play')->withTeam($team);
    }

    public function update()
    {
        return Game::first();
    }


    private function checkGame(Game $game)
    {

        $picks = $game->picks;

        // Build picks lines
        $picksLines = [];

        foreach ($picks as $pick) {
            $picksLines[$pick->team][$pick->row][$pick->col] = $pick->team;
        }

        return $this->checkStatus($picksLines, $game->current_team);


    }

    private function checkStatus($currentLines, $team)
    {

        if (isset($currentLines[$team])) {
            // Horizontal
            if (isset($currentLines[$team][0][0]) && isset($currentLines[$team][0][1]) && isset($currentLines[$team][0][2])) return 'win';
            if (isset($currentLines[$team][1][0]) && isset($currentLines[$team][1][1]) && isset($currentLines[$team][1][2])) return 'win';
            if (isset($currentLines[$team][2][0]) && isset($currentLines[$team][2][1]) && isset($currentLines[$team][2][2])) return 'win';

            // Vertical
            if (isset($currentLines[$team][0][0]) && isset($currentLines[$team][1][0]) && isset($currentLines[$team][2][0])) return 'win';
            if (isset($currentLines[$team][0][1]) && isset($currentLines[$team][1][1]) && isset($currentLines[$team][2][1])) return 'win';
            if (isset($currentLines[$team][0][2]) && isset($currentLines[$team][1][2]) && isset($currentLines[$team][2][2])) return 'win';


            // Diagonal
            if (isset($currentLines[$team][0][0]) && isset($currentLines[$team][1][1]) && isset($currentLines[$team][2][2])) return 'win';
            if (isset($currentLines[$team][0][2]) && isset($currentLines[$team][1][1]) && isset($currentLines[$team][2][0])) return 'win';
        }



        if (Pick::all()->count() === 9) {
            return 'draw';
        }

        return 'continue';

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


        $game = Game::find($game->id);

        $status = $this->checkGame($game);

        ++$game->turn_number;

        if ($status === 'win') {
            event(new Win());
        } else if ($status === 'draw') {
            event(new Draw());
        } else {
            //Continue
            event(new NextTurn());
        }

        return response(['status' => $game->save()]);

    }
}
