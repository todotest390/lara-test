@extends('layout')

{{-- Page title --}}
@section('title') Verify 2FA @stop

{{-- local styles --}}
@section('header_styles')
@stop

{{-- local scripts --}}
@section('footer_scripts')
    <script type="text/javascript">
        $('body').addClass('login');
    </script>
@stop

@section('footer_scripts_notification')
    <script type="text/javascript">
        $(document).ready(function () {
            @if(Session::has('message'))
            toastr.success("{{ Session::get('message') }}");
            @endif
        });
    </script>
@stop


@section('content')
    <div class="container">
        <div class="auth-box">
            <div class="login-table">
                <div class="logincol colleft width36">
                    <div class="login-box form-box">
                        <div class="logo-div">
                            {{--<img src="{{url('img/logo.png')}}" alt="logo" />--}}
                        </div>
                        <h1>Verify 2FA</h1>

                        <form method="POST" action="{{ url('verify-token') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                                <input id="token" type="number" class="form-control" placeholder="XXXXXX"
                                       name="one_time_password" value="" required autofocus>

                                @if ($errors->has('one_time_password'))
                                    <small class="help-block text-danger">
                                        {{ $errors->first('one_time_password') }}
                                    </small>
                                @endif
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="secret" value="{{$secret}}"/>
                                <button type="submit" class="btn btn-lg btn-primary" style="width: 220px;">
                                    Verify
                                </button>
                            </div>
                        </form>
                    </div>

                </div>


                <div class="logincol loginright colright width64">
                    <h5>Forgot 2FA Code?</h5>
                    <p><a href="{{url('forget-2fa')}}">Login with secret key</a></p>
                </div>
            </div>
        </div>
@endsection
