@extends('crewmate::layout')

@section('content')
    <div class="container">
        <div class="row justify-content-center my-5">
            <div class="col-sm-12 col-md-8 col-lg-5 my-5">
                <div class="card shadow-sm px-3">
                    <div class="card-body">

                        <div class="mb-3">
                            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                        </div>

                        @if ($errors->any())
                            <div {!! $attributes->merge(['class' => 'alert alert-danger text-sm p-2']) !!} role="alert">
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

                        <form method="POST" action="{{ route('crewmate.password.email') }}">
                            @csrf

                            <div class="form-group">
                                <label for="email">
                                    {{ __('Email') }}
                                </label>
                                <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="email"
                                       name="email" :value="old('email')" required autofocus />
                            </div>

                            <div class="d-flex justify-content-end mt-4">

                                <button type="submit" class="btn btn-dark text-uppercase">
                                    {{ __('Email Password Reset Link') }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
