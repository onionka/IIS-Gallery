@extends('layouts.base')

@section('content')

    <div class="row">
        <div class="col-xs-12">
            <h1>Artwork management</h1>

            <p>Following table displays your artwork. You can add new artwork by filling table
               header and pressing insert button below.</p>
        </div>
    </div>

    @include("layouts.insert", [
        "table" => $table,
        "header" => $header,
        "target" => $target
    ])

    @include ('handlers/error', ["errors" => $errors])
@stop