@extends('layouts.base')

@section('content')

    <div class="row">
        <div class="col-xs-12" style="text-align: center">
            <h1>Exposition spot management</h1>
            <br>
        </div>
    </div>

    @include("layouts.insert", [
        "table" => $table,
        "header" => $header,
        "target" => $target
    ])

@stop
