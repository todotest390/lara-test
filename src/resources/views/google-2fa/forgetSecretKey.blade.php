@extends('layout')

@section('page_heading') Forgot Secret Key @stop
{{-- Page title --}}
@section('title') Forgot Secret Key @stop
{{-- local styles --}}
@section('header_styles')
    <style>
        .demoInputBox {
            padding: 10px;
            border: #F0F0F0 1px solid;
            border-radius: 4px;
            background-color: #FFF;
        }
        .demoInputBox2 {
            padding: 10px;
            border: #F0F0F0 1px solid;
            border-radius: 4px;
            background-color: #FFF;
        }
        .demoInputBox3 {
            padding: 10px;
            border: #F0F0F0 1px solid;
            border-radius: 4px;
            background-color: #FFF;
        }
    </style>
@stop
{{-- local scripts --}}
@section('footer_scripts')
@stop
@section('footer_scripts_notification')
@stop
@section('content')
    <div class="container">
        <div class="auth-wrapper">
            <div class="row">
                <div class="col-sm-6">
                    <div class="auth-box shadow regi-box">
                        <h1 class="box-title"><span>Secret Key</span></h1>
                            {{ Form::open(array('url' => 'secret-key-submit')) }}

                                <div class="form-group ">
                                    <label>Secret Key</label>
                                    <input id="secret_key" type="text" class="form-control form-control-lg" name="key"
                                           placeholder="Secret Key" value="{{ old('key') }}" maxlength="100" required>
                                    <small class="help-block text-danger">{{ $errors->first('key') }}</small>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-default">Submit</button>
                                </div>


                        {{ Form::close() }}
                        <a href="{{url('key-mail-send')}}">Forget Secret Key</a>

                    </div>
            </div>
        </div></div>
    </div>
@endsection