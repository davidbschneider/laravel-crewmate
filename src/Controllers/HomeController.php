<?php

namespace DavidSchneider\LaravelCrewmate\Controllers;

class HomeController
{
    /**
     * Show the Admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        return view('crewmate::dashboard');
    }
}
