@extends('layouts.main')

@section('title', 'Конструктор ПК')

@section('content')
    <div class="your-pc-page">
        <h1 class="your-pc-page__title">Ваш компьютер</h1>
        <div class="your-pc-page__success hide">Готово!</div>
        <div class="your-pc-page__list product-list">
            <form action="/your-pc"
                  method="post"
                  class="your-pc-page__form"
                  name="form-remove-component"
                  id="form-remove-component"
            >
                @csrf
                <div class="row">
                    @foreach($configurations as $configuration)
                        <div class="col-12">
                            <div class="product-list__item">
                                <div class="product-list__title">{{htmlspecialchars_decode($configuration->name)}}</div>
                                <button type="button" class="product-list__name">{{htmlspecialchars_decode($configuration->short_name)}}</button>
                                <div class="product-list__price">{{$configuration->price}} руб.</div>
                                <button type="button" class="product-list__btn js-remove-component">Удалить</button>
                                <input type="radio"
                                       name="component-id"
                                       value="{{$configuration->config_id}}"
                                       class="radio-hidden"
                                >
                                <input type="hidden"
                                       name="action"
                                       value="remove"
                                >
                            </div>
                        </div>
                    @endforeach
                    {{--<div class="col-12">--}}
                        {{--<div class="product-list__item">--}}
                            {{--<div class="product-list__title">Видеокарта</div>--}}
                            {{--<button type="button" class="product-list__name">Gigabyte GeForce GTX 1660 Super Gaming OC 6GB GDDR6</button>--}}
                            {{--<div class="product-list__price">687.85 р.</div>--}}
                            {{--<button type="button" class="product-list__btn">Удалить</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-12">--}}
                        {{--<div class="product-list__item">--}}
                            {{--<div class="product-list__title">Оперативная память</div>--}}
                            {{--<button type="button" class="product-list__name">HyperX Fury 2x8GB DDR4 PC4-21300</button>--}}
                            {{--<div class="product-list__price">189.02 р.</div>--}}
                            {{--<button type="button" class="product-list__btn">Удалить</button>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    <div class="col-12">
                        <div class="product-list__item product-list__item_add-product">
                            <button type="button" class="product-list__add-btn btn btn_upper js-open-menu">+ Добавить комплектующее</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="your-pc-page__total total-box">
            <div class="total-box__sum">{{ '' }}</div>
            @if($percent >= 100)
                <button class="total-box__btn done btn" type="button">ЗАКАЗАТЬ</button>
            @else
                <div class="total-box__btn btn">Компьютер собран - {{ $percent }}%</div>
            @endif
            <button class="total-box__btn-share btn" type="button">ПОДЕЛИТЬСЯ</button>
        </div>
    </div>
@endsection