@extends('master')
@section('title', 'Проверка сайта')
@section('content')
<div style="margin: 0; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <div class="well well bs-component">
        <form class="form-horizontal" method="post">
            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
            <fieldset>
                <legend>Введите сайт для проверки</legend>
                <div class="form-group">
                    <div>
                        <input type="text" class="form-control" id="url" placeholder="Сайт" name="url">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <button type="submit" class="btn btn-primary">Проверить</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
@endsection