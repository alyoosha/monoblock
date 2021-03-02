@extends('layouts.main')

@section('title', 'Конструктор ПК')

@section('content')
    <div class="main-page">
        <h1 class="main-page__title">
            <span class="line">Конфигуратор</span>
            <span class="line">компьютера</span>
        </h1>
        <h2 class="main-page__greeting">Привет! Давай соберем тебе крутой компьютер!</h2>
        <div class="main-page__btn">
            <button class="btn btn_upper js-collect-pc" type="button">Системный блок</button>
        </div>
    </div>
@endsection