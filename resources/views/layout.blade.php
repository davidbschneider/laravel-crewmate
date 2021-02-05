<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="{{ asset('crewmate/css/bootstrap.min.css') }}" rel="stylesheet" />
    <title>{{ 'Crewmate@'.config('app.name') }}</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name') }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#crewmateNavbar" aria-controls="crewmateNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="crewmateNavbar">
                <ul class="navbar-nav ml-auto">
                    @auth('crewmate')
                        <a href="{{ route('crewmate.logout') }}"
                           class="nav-link"
                           onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form method="POST" id="admin-logout-form" action="{{ route('crewmate.logout') }}">
                            @csrf
                        </form>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')
    <script src="{{ asset('crewmate/js/jquery-3.5.1.slim.min.js') }}"></script>
    <script src="{{ asset('crewmate/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
