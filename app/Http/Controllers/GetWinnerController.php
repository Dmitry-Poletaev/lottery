<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class GetWinnerController extends Controller
{
    public function getWinner(Request $request)
    {
        $winner = new User();
        return $winner->getWinner($request->name);
        
    }
}
