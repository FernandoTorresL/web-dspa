<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PagesController extends Controller
{
    public function home()
    {
        Log::info('Visitando WELCOME');
//        $messages = Message::paginate(10);

        //Test login with another id
        Auth::LoginUsingID(9);

        return view('welcome', [
//            'messages' => $messages,
        ]);
    }
}
