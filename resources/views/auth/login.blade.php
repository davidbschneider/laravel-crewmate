@extends('crewmate::layout')

@section('content')
    <div class="container">
        <div class="row justify-content-center my-5">
            <div class="col-sm-12 col-md-8 col-lg-5 my-5">
                <div class="card shadow-sm px-3">
                    <div class="card-body">
                        <h1 class="mb-5 text-center">{{ __('Crewmate Login') }}</h1>
                        @if ($errors->any())
                            <div class="alert alert-danger text-sm p-2" role="alert">
                                <div class="font-weight-bold">{{ __('Whoops! Something went wrong.') }}</div>

                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-success mb-3 rounded-0" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('crewmate.login') }}">
                            @csrf
                            <div class="form-group">
                                <label for="email">
                                    {{ __('Email') }}
                                </label>
                                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email"
                                             name="email" :value="old('email')" required />
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">
                                    {{ __('Password') }}
                                </label>
                                <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" type="password"
                                             name="password" required autocomplete="current-password" />
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-0">
                                <div class="d-flex justify-content-end align-items-baseline">
                                    @if (Route::has('crewmate.password.request'))
                                        <a class="text-muted mr-3" href="{{ route('crewmate.password.request') }}">
                                            {{ __('Forgot your password?') }}
                                        </a>
                                    @endif
                                    <button type="submit" class="btn btn-dark text-uppercase">
                                        {{ __('Login') }}
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
