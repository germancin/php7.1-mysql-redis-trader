@extends((request()->ajax())? 'core::layouts.ajax-bsmodal':'core::layouts.login')

@section((request()->ajax())? 'bs-content':'content')

    @include('core::partials.flash-messages')

    <div class="body-sign">
        <div class="center-sign">
            <a href="/" class="logo float-left">
                <img src="/assets/images/ceertia-b-logo.png" height="54" alt="CEErtia">
            </a>

            <div class="panel card-sign">
                <div class="card-title-sign mt-3 text-right">
                    <h2 class="title text-uppercase font-weight-bold m-0"><i class="fas fa-user mr-1"></i>{{ trans('user::auth.btn.create-password') }}</h2>
                </div>

                <div class="card-body">
                    {!! Form::open(['route' => ['register.set_save_password'] , 'role'=>"form", 'method'=>"POST"] ) !!}

                    <div class="form-group radio-toolbar-container">
                        <div class="row">
                            <h3 class="col-sm-12 col-xs-12 pick-profile">
                                {{ trans('user::auth.form.welcome') }}
                            </h3>
                        </div>
                    </div>

                    <div class="form-inputs form_register">

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="control-label">{{ trans('user::auth.form.email') }}</label>
                            <input id="email" type="email" class="form-control form-control-lg {{ $errors->has('email') ? 'is-invalid' : '' }}" name="email" value="{{ $email }}" readonly>
                            <input id="inviter_id" type="hidden" class="form-control form-control-lg " name="inviter_id" value="{{ app('request')->input('inv_id') }}" readonly>

                        @if ($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="control-label"> {{ trans('user::auth.form.password') }} </label>
                            <input id="password" type="password" class="form-control form-control-lg {{ $errors->has('password') ? 'is-invalid' : '' }}" name="password">
                            <p class="help-block" style="font-size: 12px">{{ trans('user::auth.form.password-help') }}</p>

                        @if ($errors->has('password'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="control-label"> {{ trans('user::auth.form.password_confirmation') }} </label>
                            <input id="password-confirm" type="password" class="form-control form-control-lg {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" name="password_confirmation">
                            @if ($errors->has('password_confirmation'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('password_confirmation') }}
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-sm-8">
                                <div class="checkbox-custom checkbox-default">
                                    <input id="agree_terms" name="agree_terms" type="checkbox" value="1" required>
                                    <label for="agree_terms">{{ trans('user::auth.form.agree') }} <a href="/term-conditions">{{ trans('user::auth.form.terms') }}</a></label>
                                </div>
                            </div>
                            <div class="text-center btn-register-container">
                                <button type="submit" class="btn btn-primary btn-lg btn-register">
                                    {{ trans('user::auth.btn.set-password') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
