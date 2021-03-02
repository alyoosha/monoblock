@extends('layouts.main')

@section('title', 'Конструктор ПК')

@section('content')
    <catalog-index
        raw_components="{{$components}}"
        raw_feature_types="{{$featureTypes}}"
        raw_section="{{$section}}"
        raw_filter_relations="{{$dataFilterRelations}}"
        raw_features="{{$features}}"
        raw_csrf="{{csrf_token()}}"
    ></catalog-index>
    {{--<div class="category-page">--}}
        {{--<h1 class="category-page__title">{{$section->name}}</h1>--}}
        {{--<filter-index></filter-index>--}}
        {{--<div class="category-page__form">--}}
            {{--<!-- TODO('реализовать логику отображения выбранных фильтров') -->--}}
            {{--@if(true)--}}
                {{--<div class="category-page__formgroup category-page__formgroup_radio">--}}
                    {{--<div class="category-page__formtitle">Материнская плата</div>--}}
                    {{--<ul class="list list_unstyled tag-group">--}}
                        {{--<li class="list-item">--}}
                            {{--<div class="tag-block">--}}
                                {{--<div class="tag-block__title">AMD</div>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                        {{--<li class="list-item">--}}
                            {{--<div class="tag-block">--}}
                                {{--<div class="tag-block__title">AM4</div>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                    {{--<button class="category-page__formclean" type="button" aria-label="Очистить">X</button>--}}
                {{--</div>--}}
                {{--<fieldset class="category-page__formgroup">--}}
                    {{--<ul class="list list_unstyled tag-list">--}}
                        {{--<li class="list-item">--}}
                            {{--<div class="tag-block">--}}
                                {{--<div class="tag-block__title">Кол-во ядер - 6</div>--}}
                                {{--<button class="tag-block__remove" type="button" aria-label="удалить критерий">X</button>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                        {{--<li class="list-item">--}}
                            {{--<div class="tag-block">--}}
                                {{--<div class="tag-block__title">Кол-во ядер - 4</div>--}}
                                {{--<button class="tag-block__remove" type="button" aria-label="удалить критерий">X</button>--}}
                            {{--</div>--}}
                        {{--</li>--}}
                    {{--</ul>--}}
                {{--</fieldset>--}}
            {{--@endif--}}
        {{--</div>--}}
        {{--@php--}}
        {{--var_dump(count($components));--}}
        {{--@endphp--}}
        {{--<div class="category-page__results">--}}
            {{--<form class="category-page__form"--}}
                {{--id="form-choose-component"--}}
                {{--name="form-choose-component"--}}
                {{--action="/your-pc"--}}
                {{--method="post"--}}
            {{-->--}}
                {{--@csrf--}}
                {{--<div class="result-item result-item_rec">--}}
                    {{--<div class="result-item__content">--}}
                        {{--<div class="result-item__label">Рекомендуем</div>--}}
                        {{--<div class="result-item__title">Intel® Core™ i5-10600K(F)</div>--}}
                        {{--<div class="result-item__price">150 р.</div>--}}
                    {{--</div>--}}
                    {{--<div class="result-item__control">--}}
                        {{--<button class="btn btn_green js-choose-component" type="button">Выбрать</button>--}}
                        {{--<input class="radio-hidden" type="radio" name="component-id" value="required">--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--@foreach($components as $component)--}}
            {{--<div class="result-item">--}}
                {{--<div class="result-item__content">--}}
                    {{--<div class="result-item__title">--}}
                        {{--<a href="javascript:void(0)"--}}
                           {{--class="result-item__link js-open-component"--}}
                           {{--data-id="{{$component->id}}">{{$component->short_name}}--}}
                        {{--</a>--}}
                    {{--</div>--}}
                    {{--<div class="result-item__price">{{$component->price}} руб</div>--}}
                {{--</div>--}}
                {{--<div class="result-item__control">--}}
                    {{--<button class="btn btn_green js-choose-component" type="button">Выбрать</button>--}}
                    {{--<input class="radio-hidden" type="radio" name="component-id" value="{{$component->id}}">--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--@endforeach--}}
                {{--<input type="hidden"--}}
                       {{--name="action"--}}
                       {{--value="add"--}}
                {{-->--}}
            {{--</form>--}}
        {{--</div>--}}
        {{--<div class="panel panel_filters">--}}
            {{--<div class="panel__content">--}}
                {{--<div class="panel__header">--}}
                    {{--<div class="panel__title">{{$section->name}}<span class="panel__subtitle">выбор характеристик</span></div>--}}
                    {{--<button class="panel__close js-open-filter" type="button" aria-label="Закрыть панель фильтров">Х</button>--}}
                {{--</div>--}}
                {{--<div class="panel__body">--}}
                    {{--<form class="category-page__form"--}}
                        {{--name="form-set-filter"--}}
                        {{--id="form-set-filter"--}}
                        {{--method="get"--}}
                        {{--action="">--}}
                        {{--<!-- Логика отображения фильта накладывающего ограничения -->--}}
                        {{--@if(false)--}}
                            {{--<fieldset class="category-page__formgroup category-page__formgroup_radio">--}}
                                {{--<div class="category-page__formtitle">Материнская плата--}}
                                    {{--<ul class="list list_unstyled tag-group">--}}
                                        {{--<li class="list-item">--}}
                                            {{--<div class="tag-block">--}}
                                                {{--<div class="tag-block__title">AMD</div>--}}
                                            {{--</div>--}}
                                        {{--</li>--}}
                                        {{--<li class="list-item">--}}
                                            {{--<div class="tag-block">--}}
                                                {{--<div class="tag-block__title">AM4</div>--}}
                                            {{--</div>--}}
                                        {{--</li>--}}
                                    {{--</ul>--}}
                                    {{--<button class="category-page__formclean" type="button" aria-label="Очистить">X</button>--}}
                                {{--</div>--}}
                            {{--</fieldset>--}}
                        {{--@endif--}}
                        {{--@foreach($featureTypes as $featureType)--}}
                            {{--@if(!$featureType->features->isEmpty() || !$featureType->values->isEmpty())--}}
                                {{--<fieldset class="category-page__formgroup">--}}
                                    {{--<div class="category-page__formheader">--}}
                                        {{--<div class="category-page__label">{{$featureType->name}}</div>--}}
                                    {{--</div>--}}
                                    {{--@if($featureType->filter_type == 'list')--}}
                                        {{--<ul class="list list_unstyled filter-list">--}}
                                            {{--@foreach($featureType->features as $feature)--}}
                                                {{--@if(isset($paramsFilter['features']['list']) &&--}}
                                                    {{--array_key_exists($featureType->id, $paramsFilter['features']['list'])--}}
                                                     {{--&& in_array($feature->id, $paramsFilter['features']['list'][$featureType->id])--}}
                                                {{--)--}}
                                                    {{--<li class="checkbox">--}}
                                                        {{--<label class="checkbox-label">--}}
                                                            {{--<input class="checkbox-control"--}}
                                                                   {{--type="checkbox"--}}
                                                                   {{--name="{{$featureType->slug}}%L{{$featureType->id}}%D{{$feature->id}}"--}}
                                                                   {{--value="{{$feature->slug}}"--}}
                                                                   {{--checked="checked"--}}
                                                            {{-->--}}
                                                            {{--<span class="checkbox-custom">{{$feature->name}}</span>--}}
                                                        {{--</label>--}}
                                                    {{--</li>--}}
                                                {{--@else--}}
                                                    {{--<li class="checkbox">--}}
                                                        {{--<label class="checkbox-label">--}}
                                                            {{--<input class="checkbox-control"--}}
                                                                   {{--type="checkbox"--}}
                                                                   {{--name="{{$featureType->slug}}%L{{$featureType->id}}%D{{$feature->id}}"--}}
                                                                   {{--value="{{$feature->slug}}"--}}
                                                            {{-->--}}
                                                            {{--<span class="checkbox-custom">{{$feature->name}}</span>--}}
                                                        {{--</label>--}}
                                                    {{--</li>--}}
                                                {{--@endif--}}
                                            {{--@endforeach--}}
                                        {{--</ul>--}}
                                    {{--@elseif($featureType->filter_type == 'number')--}}
                                        {{--@php--}}
                                            {{--$sorted = $featureType->values->sortBy(function ($value) {--}}
                                                {{--return (int) $value['value'];--}}
                                            {{--});--}}
                                        {{--@endphp--}}
                                        {{--@if(isset($paramsFilter['values']['number'][$featureType->id]['values']['from']))--}}
                                            {{--<input type="number"--}}
                                                   {{--min="{{$sorted->first()->value}}"--}}
                                                   {{--name="from%N{{$featureType->slug}}%R{{$featureType->id}}"--}}
                                                   {{--placeholder="{{$sorted->first()->value}}"--}}
                                                   {{--value="{{$paramsFilter['values']['number'][$featureType->id]['values']['from']}}"--}}
                                            {{-->--}}
                                        {{--@else--}}
                                            {{--<input type="number"--}}
                                                   {{--min="{{$sorted->first()->value}}"--}}
                                                   {{--name="from%N{{$featureType->slug}}%R{{$featureType->id}}"--}}
                                                   {{--placeholder="{{$sorted->first()->value}}"--}}
                                            {{-->--}}
                                        {{--@endif--}}
                                        {{--@if(isset($paramsFilter['values']['number'][$featureType->id]['values']['to']))--}}
                                            {{--<input type="number"--}}
                                                   {{--max="{{$sorted->last()->value}}"--}}
                                                   {{--name="to%N{{$featureType->slug}}%R{{$featureType->id}}"--}}
                                                   {{--placeholder="{{$sorted->last()->value}}"--}}
                                                   {{--value="{{$paramsFilter['values']['number'][$featureType->id]['values']['to']}}"--}}
                                            {{-->--}}
                                        {{--@else--}}
                                            {{--<input type="number"--}}
                                                   {{--max="{{$sorted->last()->value}}"--}}
                                                   {{--name="to%N{{$featureType->slug}}%R{{$featureType->id}}"--}}
                                                   {{--placeholder="{{$sorted->last()->value}}"--}}
                                            {{-->--}}
                                        {{--@endif--}}
                                    {{--@elseif($featureType->filter_type == 'string')--}}
                                        {{--<ul class="list list_unstyled filter-list">--}}
                                            {{--@foreach($featureType->values as $value)--}}
                                                {{--@if(isset($paramsFilter['values']['string']) &&--}}
                                                    {{--array_key_exists($featureType->id, $paramsFilter['values']['string'])--}}
                                                     {{--&& in_array($value->value, $paramsFilter['values']['string'][$featureType->id])--}}
                                                {{--)--}}
                                                    {{--<li class="checkbox">--}}
                                                        {{--<label class="checkbox-label">--}}
                                                        {{--<input class="checkbox-control"--}}
                                                               {{--type="checkbox"--}}
                                                               {{--name="{{$featureType->slug}}%S{{$value->id}}%T{{$featureType->id}}"--}}
                                                               {{--value="{{$value->value}}"--}}
                                                               {{--checked="checked "--}}
                                                        {{-->--}}
                                                        {{--<span class="checkbox-custom">{{$value->value}}</span>--}}
                                                        {{--</label>--}}
                                                    {{--</li>--}}
                                                {{--@else--}}
                                                    {{--<li class="checkbox">--}}
                                                        {{--<label class="checkbox-label">--}}
                                                            {{--<input class="checkbox-control"--}}
                                                                   {{--type="checkbox"--}}
                                                                   {{--name="{{$featureType->slug}}%S{{$value->id}}%T{{$featureType->id}}"--}}
                                                                   {{--value="{{$value->value}}"--}}
                                                            {{-->--}}
                                                            {{--<span class="checkbox-custom">{{$value->value}}</span>--}}
                                                        {{--</label>--}}
                                                    {{--</li>--}}
                                                {{--@endif--}}
                                            {{--@endforeach--}}
                                        {{--</ul>--}}
                                    {{--@elseif($featureType->filter_type == 'boolean')--}}
                                        {{--<ul class="list list_unstyled filter-list">--}}
                                            {{--<li class="radio">--}}
                                                {{--<label class="radio-label">--}}
                                                {{--<input class="radio-control"--}}
                                                       {{--type="radio"--}}
                                                       {{--name="{{$featureType->slug}}%B{{$featureType->id}}"--}}
                                                       {{--value="1"--}}
                                                {{-->--}}
                                                {{--<span class="radio-custom">Есть</span>--}}
                                                {{--</label>--}}
                                            {{--</li>--}}
                                            {{--<li class="radio">--}}
                                                {{--<label class="radio-label">--}}
                                                {{--<input class="radio-control"--}}
                                                       {{--type="radio"--}}
                                                       {{--name="{{$featureType->slug}}%B{{$featureType->id}}"--}}
                                                       {{--value="0"--}}
                                                {{-->--}}
                                                {{--<span class="radio-custom">Нет</span>--}}
                                                {{--</label>--}}
                                            {{--</li>--}}
                                        {{--</ul>--}}
                                    {{--@endif--}}
                                {{--</fieldset>--}}
                            {{--@endif--}}
                        {{--@endforeach--}}
                    {{--</form>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="panel__footer">--}}
                {{--<div class="row justify-content-center">--}}
                    {{--<div class="col-auto">--}}
                        {{--<button class="btn btn_blue filter-btn-main" type="submit" form="form-set-filter">Найти</button>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<div class="panel panel_product" id="panel-component">--}}
            {{--<div class="panel__content">--}}
                {{--<div class="panel__content">--}}
                    {{--<div class="panel__header">--}}
                        {{--<div class="panel__title">{{$section->name}}</div>--}}
                        {{--<button class="panel__close js-close-component" type="button" aria-label="Закрыть панель компонента">Х</button>--}}
                    {{--</div>--}}
                    {{--<div class="panel__body">--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
@endsection