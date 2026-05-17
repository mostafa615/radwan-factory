@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div>
            <div class="">
                <div class="login-logo" style="text-align: center;">
                    <img style="border-radius: 45px" src="{{ asset('dashboard/images/login/logo.png') }}" class="w3-center w3-round"  width="90px" >
                    <br>
                    {{-- <h1 style="color: #fff"  class="w3-text-white" ><b> @lang('site.fcis-medical')  </b></h1> --}}
                    <h1 style="color: #fff"  class="w3-text-white" ><b>@lang('site.atis') </b></h1>

                </div>

                <div class="login-box">

                    <div class="login-box-body">

                            <p class="login-box-msg" style="color: red">@lang('site.login-not2')</p>
                            <br>

                            <form method="POST" action="{{ route('attend.store') }}" class="std-log-card">
                                @csrf

                                    <div class="form-group has-feedback">
                                        <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="@lang('site.national_id')">
                                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            {{-- {{ __('Login') }} --}} @lang('site.sign-attend')
                                        </button>

                                        <a href="{{route('leave.store')}}" class="btn btn-primary btn-block">@lang('site.sign-leave')</a>

                                    </div>


                                        {{-- <div class="social-auth-links text-center mb-3">
                                            <a href="#" class="btn btn-block btn-success btn-flat">
                                               @lang('site.register')
                                            </a>

                                          </div> --}}

                                        @if (Route::has('password.request'))
                                            {{-- <a class="btn btn-link" href="#{{-- route('password.request') --">
                                                {{-- {{ __('Forgot Your Password?') }} --
                                                @lang('site.forgot-password')
                                            </a> --}}
                                        @endif
                            </form>

                            {{-- <form action="{{ url('/') }}/dashboard/complain/store" class="problem-card" method="post" style="display: none">
                                {{ csrf_field() }}
                                <div class="form-group has-feedback">
                                    {{-- <label>{{ __("i'm a ") }}</label> --}
                                    <select name="type" class="form-control" required  onchange="this.value == 'student'? $('.complaint-code-student').show(300).find('input').attr('required', 'required') : $('.complaint-code-student').hide(300).find('input').removeAttr('required')" >
                                        <option value="student">{{ __('site.student') }}</option>
                                        <option value="doctor">{{ __('site.doctor') }}</option>
                                    </select>
                                    {{-- <span class="glyphicon glyphicon-user form-control-feedback"></span> --}
                                </div>
                                <div class="form-group has-feedback complaint-code-student">
                                    {{-- <label>{{ __('site.code') }}</label> --}
                                    <input required=""  type="text" name="code" class="form-control" placeholder="{{ __('site.code') }}">
                                    {{-- <span class="fa fa-barcode form-control-feedback"></span> --}
                                </div>
                                <div class="form-group has-feedback">
                                    {{-- <label>{{ __('site.name') }}</label> --}
                                    <input required="" type="text" name="name" class="form-control" placeholder="{{ __('site.name') }}">
                                    {{-- <span class="fa fa-user form-control-feedback"></span> --}
                                </div>
                                <div class="form-group has-feedback">
                                    {{-- <label>{{ __('phone') }}</label> --}
                                    <input required="" type="text" name="phone" class="form-control" placeholder="{{ __('site.phone') }}">
                                    {{-- <span class="glyphicon glyphicon-phone form-control-feedback"></span> --}
                                </div>
                                <div class="form-group has-feedback">
                                    <label>{{ __('site.your_problem') }}</label>
                                    <textarea required class="form-control" name="notes" ></textarea>
                                    {{-- <span class="fa fa-edit form-control-feedback"></span> --}
                                </div>
                                <br>
                                <div class="">
                                    <!-- /.col -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block btn-flat">{{ __('site.send') }}</button>

                                        <button
                                        type="button"
                                        onclick="$('.problem-card').slideUp(500);$('.std-log-card').slideDown(500)"
                                        class="btn btn-success btn-block btn-flat">{{ __('site.back') }}</button>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </form> --}}
                            <br>

                            {{-- <p class="login-box-msg" style="color: red;font-size:17px; cursor: pointer;" onclick="$('.auth-container, .std-log-card').slideUp(500);$('.problem-card').slideDown(500)">@lang('site.login-not4') <i class="fa fa-frown-o" style="padding: 5px"></i> </p> --}}

                    </div>
                </div><!--end card -->

                {{-- <div class="card">
                    <div class="card-body login-card-body">
                        <p class="login-box-msg">@lang('site.regist-on-your-dashboard')</p>

                      <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="input-group mb-3">
                          <input type="text" class="form-control" placeholder="@lang('site.phone')">
                          <div class="input-group-append">
                            <div class="input-group-text">
                              <span class="fas fa-envelope"></span>
                            </div>
                          </div>
                        </div>
                        <div class="input-group mb-3">
                          <input type="password" class="form-control" placeholder="@lang('site.password')">
                          <div class="input-group-append">
                            <div class="input-group-text">
                              <span class="fas fa-lock"></span>
                            </div>
                          </div>
                        </div>

                          <!-- /.col -->

                            <button type="submit" class="btn btn-primary btn-block btn-flat">@lang('site.sign-in')</button>
                          <!-- /.col -->
                      </form>

                      <div class="social-auth-links text-center mb-3">
                        <a href="#" class="btn btn-block btn-success btn-flat">
                           @lang('site.register')
                        </a>

                      </div>
                      <!-- /.social-auth-links -->

                      <p class="mb-1">
                        <a href="#">@lang('site.forgot-password')</a>
                      </p>

                    </div>
                    <!-- /.login-card-body -->
                  </div>

                </div> --}}

        </div>
    </div>
</div>
@endsection





