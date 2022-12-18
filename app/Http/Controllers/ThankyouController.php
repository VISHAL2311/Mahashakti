<?php



namespace App\Http\Controllers;



use Illuminate\Support\Facades\Redirect;

use Request;

use Session;

use Config;



class ThankyouController extends FrontController {



    public function __construct() {

        parent::__construct();

    }



    public function index(Request $request) {

        if (Session::get('form_submit')) {

            view()->share('META_TITLE', "Thank You for contacting - Avalon Marine Group");

            view()->share('META_KEYWORD', "Thank You");

            view()->share('META_DESCRIPTION', "Thank You for contacting Avalon Marine Group get back to you shortly");

            return view('thank-you', ['message' => Session::get('message')]);

        } else {

            return redirect('/');

        }

    }



    public function subscribe_failed(Request $request) {

        if (Session::get('form_submit')) {

            view()->share('META_TITLE', "Thank You for contacting - Avalon Marine Group");

            view()->share('META_KEYWORD', "Thank You");

            view()->share('META_DESCRIPTION', "Thank You for contacting Avalon Marine Group get back to you shortly");

            return view('failed', ['message' => Session::get('message')]);

        } else {

            return redirect('/');

        }

    }



    public function dataremoval_failed(Request $request) {

        if (Session::get('form_submit')) {

            view()->share('META_TITLE', "Thank You for contacting - Avalon Marine Group");

            view()->share('META_KEYWORD', "Thank You");

            view()->share('META_DESCRIPTION', "Thank You for contacting Avalon Marine Group get back to you shortly");

            return view('data-removal-failed', ['message' => Session::get('message')]);

        } else {

            return redirect('/');

        }

    }



}

