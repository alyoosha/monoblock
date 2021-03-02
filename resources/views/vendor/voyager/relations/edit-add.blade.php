@php
    $edit = !is_null($dataTypeContent->getKey());
    $add  = is_null($dataTypeContent->getKey());
@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.($edit ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    @if($add)
        <div class="page-content browse container-fluid">
        <div class="relations-block">
            <form action="/api/relations/save-relation" class="relations-block__form" name="form-add-relations" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <div class="row js-open-row js-open-row-1">
                    <div class="col-md-8 js-col">
                        <label for="relation-type" class="relations-block__label">Тип связи</label>
                        <select class="js-select-relation" name="relation-type" id="relation-type" required>
                            <option title="Выберете связь" value="">Выберете связь</option>
                            @foreach($relationTypes as $rt)
                                <option title="{{$rt->description}}" value="{{$rt->code}}">{{$rt->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 js-col-descr">
                        <div class="js-descr-relation " data-rel-type=""></div>
                        @foreach($relationTypes as $rt)
                            <div class="js-descr-relation hidden" data-rel-type="{{$rt->code}}">{!!$rt->description!!}</div>
                        @endforeach
                    </div>
                </div>
                <div class="row js-open-row js-open-row-2 hidden">
                    <div class="col-md-4 js-col">
                        <label for="section-first" class="relations-block__label js-label-num">Основной раздел</label>
                        {{--<label for="section-first" class="relations-block__label js-label-another hidden">Первый раздел</label>--}}
                        <select class="js-select-relation" name="section_first" data-feature-type="feature-type-first" id="section-first" required>
                            <option value="">Выберете раздел</option>
                            @foreach($sections as $s)
                                <option value="{{$s->id}}">{{$s->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 js-col">
                        <label for="section-second" class="relations-block__label js-label-num">Связываемый раздел</label>
                        {{--<label for="section-second" class="relations-block__label js-label-another hidden">Второй раздел</label>--}}
                        <select class="js-select-relation" name="section_second" data-feature-type="feature-type-second" id="section-second" required>
                            <option value="">Выберете раздел</option>
                            @foreach($sections as $s)
                                <option value="{{$s->id}}">{{$s->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row js-open-row js-open-row-3 hidden">
                    <div class="col-md-4 js-col">
                        <label for="feature-type-first" class="relations-block__label js-label-num">Основная характеристика</label>
                        {{--<label for="feature-type-first" class="relations-block__label js-label-another hidden">Первая характеристика</label>--}}
                        <select class="js-select-relation" name="feature_type_first" data-feature="features-first" data-is-first="1" id="feature-type-first">
                        </select>
                    </div>
                    <div class="col-md-4 js-col js-col-hidden">
                        <label for="feature-type-second" class="relations-block__label js-label-num">Связываемая характеристика</label>
                        {{--<label for="feature-type-second" class="relations-block__label js-label-another hidden">Вторая характеристика</label>--}}
                        <select class="js-select-relation" name="feature_type_second" data-feature="features-second" data-is-first="0" id="feature-type-second">
                        </select>
                    </div>
                </div>
                <div class="row js-open-row js-open-row-4 hidden">
                    <div class="col-md-4 js-col-select js-2cols-hidden hidden">
                        <label for="feature-first-select" class="relations-block__label js-label-num">Основное значение</label>
                        {{--<label for="feature-first-select" class="relations-block__label js-label-another hidden">Первое значение</label>--}}
                        <select name="feature-first-select" id="feature-first-select" data-is-first="1">
                        </select>
                    </div>
                    <div class="col-md-4 js-col js-col-checkbox js-col-checkbox-1 js-2cols-hidden">
                        <label for="features-first" class="relations-block__label js-label-num">Основное значение</label>
                        {{--<label for="features-first" class="relations-block__label js-label-another hidden">Первое значение</label>--}}
                        <fieldset id="features-first">
                        </fieldset>
                    </div>
                    <div class="col-md-4 js-col js-col js-col-checkbox js-col-checkbox-2 js-col-hidden js-2cols-hidden">
                        <label for="features-second" class="relations-block__label js-label-num">Связываемое значение</label>
                        {{--<label for="features-second" class="relations-block__label js-label-another hidden">Второе значение</label>--}}
                        <fieldset id="features-second">
                        </fieldset>
                    </div>
                    <div class="col-md-8">
                        <button type="submit" class="relations-block  js-save-relation btn btn-success" disabled="disabled">Сохранить связь</button>
                    </div>
                </div>
            </form>
            <a href="/admin/relations" class="relations-block__back btn btn-dark">Назад</a>
        </div>
    </div>
    @endif

    @if($edit)
        <div class="page-content browse container-fluid">
            <div class="relations-block">
                <form action="/api/relations/edit-relation" class="relations-block__form" name="form-edit-relations" method="post" enctype="multipart/form-data">
                    <div class="row js-open-row js-open-row-1">
                        <div class="col-md-8 js-col">
                            <label for="relation-type" class="relations-block__label">Тип связи</label>
                            <select class="js-select-relation" name="relation-type" id="relation-type" required disabled>
                                <option title="Выберете связь" value="">Выберете связь</option>
                                @foreach($relationTypes as $rt)
                                    @if($rt->code == $relationType)
                                        <option title="{{$rt->description}}" value="{{$rt->code}}" selected>{{$rt->name}}</option>
                                    @else
                                        <option title="{{$rt->description}}" value="{{$rt->code}}">{{$rt->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 js-col-descr">
                            <div class="js-descr-relation " data-rel-type=""></div>
                            @foreach($relationTypes as $rt)
                                <div class="js-descr-relation hidden" data-rel-type="{{$rt->code}}">{{$rt->description}}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="row js-open-row js-open-row-2">
                        <div class="col-md-4 js-col">
                            <label for="section-first" class="relations-block__label">Первый раздел</label>
                            <select class="js-select-relation selected" name="section_first" data-feature-type="feature-type-first" id="section-first" disabled>
                                <option value="">Выберете раздел</option>
                                @foreach($sections as $s)
                                    @if($s->id == $dataTypeContent->section_first)
                                        <option value="{{$s->id}}" selected>{{$s->name}}</option>
                                    @elseif($s->id == $dataTypeContent->section_second)
                                        <option value="{{$s->id}}" disabled="true">{{$s->name}}</option>
                                    @else
                                        <option value="{{$s->id}}">{{$s->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 js-col">
                            <label for="section-second" class="relations-block__label">Второй раздел</label>
                            <select class="js-select-relation selected" name="section_second" data-feature-type="feature-type-second" id="section-second" disabled>
                                <option value="">Выберете раздел</option>
                                @foreach($sections as $s)
                                    @if($s->id == $dataTypeContent->section_second)
                                        <option value="{{$s->id}}" selected>{{$s->name}}</option>
                                    @elseif($s->id == $dataTypeContent->section_first)
                                        <option value="{{$s->id}}" disabled="true">{{$s->name}}</option>
                                    @else
                                        <option value="{{$s->id}}">{{$s->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row js-open-row js-open-row-3">
                        <div class="col-md-4 js-col">
                            <label for="feature-type-first" class="relations-block__label">Первая характеристика</label>
                            <select class="js-select-relation selected" name="feature_type_first" data-feature="features-first" data-is-first="1" id="feature-type-first" {{$relationType == 'one-to-many' ? 'disabled' : ''}}>
                                <option value="">Выберете характеристику</option>
                                @foreach($featureTypesFirst as $f)
                                    @if($f->id == $dataTypeContent->feature_type_first)
                                        <option
                                            value="{{$f->id}}"
                                            data-filter="{{$f->filter_type}}"
                                            selected
                                        >
                                            {{$f->custom_name ? $f->custom_name : $f->name}}
                                        </option>
                                    @else
                                        <option
                                            value="{{$f->id}}"
                                            data-filter="{{$f->filter_type}}"
                                        >
                                            {{$f->custom_name ? $f->custom_name : $f->name}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 js-col js-col-hidden {{$relationType == 'section' ? 'hidden' : ''}}">
                            <label for="feature-type-second" class="relations-block__label">Вторая характеристика</label>
                            <select class="js-select-relation selected" name="feature_type_second" data-feature="features-second" data-is-first="0" id="feature-type-second" {{$relationType == 'one-to-many' ? 'disabled' : ''}}>
                                <option>Выберете характеристику</option>
                                @foreach($featureTypesSecond as $f)
                                    @if($f->id == $dataTypeContent->feature_type_second)
                                        <option
                                            value="{{$f->id}}"
                                            data-filter="{{$f->filter_type}}"
                                            selected
                                        >
                                            {{$f->custom_name ? $f->custom_name : $f->name}}
                                        </option>
                                    @else
                                        <option
                                            value="{{$f->id}}"
                                            data-filter="{{$f->filter_type}}"
                                        >
                                            {{$f->custom_name ? $f->custom_name : $f->name}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row js-open-row js-open-row-4">
                        @if($relationType == 'one-to-many')
                            <div class="col-md-4 js-col col-md-4 js-col-select">
                        @else
                            <div class="col-md-4 js-col-select hidden">
                        @endif
                            <label for="feature-first-select" class="relations-block__label js-label-num">Основное значение</label>
                            <select name="feature-first-select" id="feature-first-select" data-is-first="1">
                                @if($relationType == 'one-to-many' )
                                    @if($valuesFirstAll->isNotEmpty())
                                        @foreach($valuesFirstAll as $k => $v)
                                            @php
                                                $arr = explode(',', $dataTypeContent->values_first)
                                            @endphp
                                            @if(in_array($k, $arr))
                                                <option
                                                    value='{"is_first":1,"value": "{{$k}}"}'
                                                    data-filter="{{$v->filter_type}}"
                                                    data-value="{{$k}}"
                                                    data-name="{{$k}}"
                                                    selected
                                                >{{$k}}
                                                </option>
                                            @else
                                                <option
                                                    value='{"is_first":1,"value": "{{$k}}"}'
                                                    data-filter="{{$v->filter_type}}"
                                                    data-value="{{$k}}"
                                                    data-name="{{$k}}"
                                                >{{$k}}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif

                                    @if($featuresFirstAll->isNotEmpty())
                                        @foreach($featuresFirstAll as $f)
                                            @php
                                                $arr = explode(',', $dataTypeContent->features_first)
                                            @endphp
                                            @if(in_array($f->id, $arr))
                                                <option
                                                    value='{"is_first":1,"value": "{{$f['id']}}"}'
                                                    data-filter="{{$f->filter_type}}"
                                                    data-value="{{$f->id}}"
                                                    data-name="{{$f->name}}"
                                                    selected
                                                >
                                                    {{$f->name}}
                                                </option>
                                            @else
                                                <option
                                                    value='{"is_first":1,"value": "{{$f['id']}}"}'
                                                    data-filter="{{$f->filter_type}}"
                                                    data-value="{{$f->id}}"
                                                    data-name="{{$f->name}}"
                                                >
                                                    {{$f->name}}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                @endif
                            </select>
                        </div>
                        @if($relationType == 'quantity' || $relationType == 'size' || $relationType == 'one-to-many')
                             <div class="col-md-4 js-col js-col-checkbox js-col-checkbox-1 col-md-4 hidden">
                        @else
                             <div class="col-md-4 js-col js-col-checkbox js-col-checkbox-1">
                        @endif
                            <label for="features-first" class="relations-block__label js-label-num">Основное значение</label>
                            <fieldset id="features-first">
                                @if($relationType != 'one-to-many' )
                                    @if($valuesFirstAll->isNotEmpty())
                                        @foreach($valuesFirstAll as $k => $v)
                                            @php
                                                $arr = [];
                                                if(!is_null($dataTypeContent->values_first)) {
                                                    $arr = explode(',', $dataTypeContent->values_first);
                                                }
                                            @endphp
                                            @if(in_array($k, $arr))
                                                <label class="relations-block__wrap-checkbox js-label-checkbox">
                                                    <input type="checkbox" class="relations-block__checkbox js-checkbox"
                                                        name='v-{{trim($k)}}-1'
                                                        id="v-{{trim($k)}}-1"
                                                        value='{"is_first":1,"value":"{{$k}}"}'
                                                        data-value="{{trim($k)}}"
                                                        data-name="{{trim($k)}}"
                                                        checked="checked"
                                                    >
                                                    <span class="relations-block__value">{{$k}}</span>
                                                </label>
                                            @else
                                                <label class="relations-block__wrap-checkbox js-label-checkbox">
                                                    <input type="checkbox" class="relations-block__checkbox js-checkbox"
                                                        name='v-{{trim($k)}}-1'
                                                        id="v-{{trim($k)}}-1"
                                                        value='{"is_first":1,"is_feature":0,"value":"{{$k}}"}'
                                                        data-value="{{trim($k)}}"
                                                        data-name="{{trim($k)}}"
                                                    >
                                                    <span class="relations-block__value">{{$k}}</span>
                                                </label>
                                            @endif
                                        @endforeach
                                    @endif

                                    @if($featuresFirstAll->isNotEmpty())
                                        @foreach($featuresFirstAll as $f)
                                            @php
                                                $arr = [];
                                                if(!is_null($dataTypeContent->features_first)) {
                                                    $arr = explode(',', $dataTypeContent->features_first);
                                                }
                                            @endphp
                                            @if(in_array($f->id, $arr))
                                                <label class="relations-block__wrap-checkbox js-label-checkbox">
                                                    <input type="checkbox" class="relations-block__checkbox js-checkbox"
                                                        name='f-{{$f->id}}-1'
                                                        id="f-{{$f->id}}-1"
                                                        value='{"is_first":1,"is_feature":1,"value":"{{$f->id}}"}'
                                                        checked="checked">
                                                    <span class="relations-block__value">{{$f->name}}</span>
                                                </label>
                                            @else
                                                <label class="relations-block__wrap-checkbox js-label-checkbox">
                                                    <input type="checkbox" class="relations-block__checkbox js-checkbox"
                                                        name='f-{{$f->id}}-1'
                                                        id="f-{{$f->id}}-1"
                                                        value='{"is_first":1,"is_feature":1,"value":"{{$f->id}}"}'>
                                                    <span class="relations-block__value">{{$f->name}}</span>
                                                </label>
                                            @endif
                                        @endforeach
                                    @endif
                                @endif
                            </fieldset>
                        </div>
                        @if($relationType == 'quantity' || $relationType == 'size' || $relationType == 'section')
                            <div class="col-md-4 js-col js-col-checkbox js-col-checkbox-2 col-md-4 hidden">
                        @else
                            <div class="col-md-4 js-col js-col-checkbox js-col-checkbox-2">
                        @endif
                            <label for="features-second" class="relations-block__label js-label-num">Связываемое значение</label>
                            <fieldset id="features-second">
                                @if($valuesSecondAll->isNotEmpty())
                                     @foreach($valuesSecondAll as $k => $v)
                                         @php
                                             $arr = [];
                                             if(!is_null($dataTypeContent->values_second)) {
                                                 $arr = explode(',', $dataTypeContent->values_second);
                                             }
                                         @endphp
                                         @if(in_array($k, $arr))
                                             <label class="relations-block__wrap-checkbox js-label-checkbox">
                                                 <input type="checkbox" class="relations-block__checkbox js-checkbox"
                                                     name='v-{{trim($k)}}-2'
                                                     id="v-{{trim($k)}}-2"
                                                     value='{"is_first":0,"value":"{{$k}}"}'
                                                     data-value="{{trim($k)}}"
                                                     data-name="{{trim($k)}}"
                                                     checked="checked"
                                                 >
                                                 <span class="relations-block__value">{{$k}}</span>
                                                 <span class="relations-block__wrap-relation js-wrap-relation">
                                                     @foreach($theseRelations as $r)
                                                         @php
                                                             $valueFirst = $r->values_first ? $r->values_first : $r->features_first;
                                                             $valueSecond = $r->values_second ? $r->values_second : $r->features_second;
                                                             $mainArr = $r->values_first ? $valuesFirstAll : $featuresFirstAll;
                                                             $arr = [];
                                                             $arrFirst = explode(',', $valueFirst);
                                                             $arr = explode(',', $valueSecond);
                                                         @endphp
                                                         @if(!empty($arrFirst))
                                                             @foreach($arrFirst as $f)
                                                                 @if($mainArr->has($f) && in_array($k, $arr))
                                                                     <span class="relations-block__value-relation js-value-relation">
                                                                         @if($mainArr[$f][0])
                                                                            {{$mainArr[$f][0]->name ? $mainArr[$f][0]->name : $mainArr[$f][0]->value}}
                                                                         @else
                                                                             {{$mainArr[$f]->name ? $mainArr[$f]->name : $mainArr[$f]->value}}
                                                                         @endif
                                                                     </span>
                                                                 @endif
                                                             @endforeach
                                                         @endif
                                                     @endforeach
                                                 </span>
                                             </label>
                                         @else
                                             <label class="relations-block__wrap-checkbox js-label-checkbox">
                                                 <input type="checkbox" class="relations-block__checkbox js-checkbox"
                                                     name='v-{{trim($k)}}-2'
                                                     id="v-{{trim($k)}}-2"
                                                     value='{"is_first":0,"value":"{{$k}}"}'
                                                     data-value="{{trim($k)}}"
                                                     data-name="{{trim($k)}}"
                                                 >
                                                 <span class="relations-block__value">{{$k}}</span>
                                                 <span class="relations-block__wrap-relation js-wrap-relation">
                                                     @foreach($theseRelations as $r)
                                                         @php
                                                             $valueFirst = $r->values_first ? $r->values_first : $r->features_first;
                                                             $valueSecond = $r->values_second ? $r->values_second : $r->features_second;
                                                             $mainArr = $r->values_first ? $valuesFirstAll : $featuresFirstAll;
                                                             $arr = [];
                                                             $arrFirst = explode(',', $valueFirst);
                                                             $arr = explode(',', $valueSecond);
                                                         @endphp
                                                         @if(!empty($arrFirst))
                                                             @foreach($arrFirst as $f)
                                                                 @if($mainArr->has($f) && in_array($k, $arr))
                                                                     <span class="relations-block__value-relation js-value-relation">
                                                                         @if($mainArr[$f][0])
                                                                             {{$mainArr[$f][0]->name ? $mainArr[$f][0]->name : $mainArr[$f][0]->value}}
                                                                         @else
                                                                             {{$mainArr[$f]->name ? $mainArr[$f]->name : $mainArr[$f]->value}}
                                                                         @endif
                                                                     </span>
                                                                 @endif
                                                             @endforeach
                                                         @endif
                                                     @endforeach
                                                 </span>
                                             </label>
                                         @endif
                                     @endforeach
                                @endif

                                @if($featuresSecondAll->isNotEmpty())
                                    @foreach($featuresSecondAll as $f)
                                        @php
                                            $arr = [];
                                            if(!is_null($dataTypeContent->features_second)) {
                                                $arr = explode(',', $dataTypeContent->features_second);
                                            }
                                        @endphp
                                        @if(in_array($f->id, $arr))
                                            <label class="relations-block__wrap-checkbox js-label-checkbox">
                                                <input type="checkbox" class="relations-block__checkbox js-checkbox"
                                                    name='f-{{$f->id}}-2'
                                                    id="f-{{trim($f->id)}}-2"
                                                    value='{"is_first":0,"is_feature":1,"value":"{{$f->id}}"}'
                                                    data-value="{{$f->id}}"
                                                    data-name="{{trim($f->name)}}"
                                                    checked="checked"

                                                >
                                                <span class="relations-block__value">{{$f->name}}</span>
                                                <span class="relations-block__wrap-relation js-wrap-relation">
                                                    @foreach($theseRelations as $r)
                                                        @php
                                                            $valueFirst = $r->values_first ? $r->values_first : $r->features_first;
                                                            $valueSecond = $r->values_second ? $r->values_second : $r->features_second;
                                                            $mainArr = $r->values_first ? $valuesFirstAll : $featuresFirstAll;
                                                            $arr = [];
                                                            $arrFirst = explode(',', $valueFirst);
                                                            $arr = explode(',', $valueSecond);
                                                        @endphp
                                                        @if(!empty($arrFirst))
                                                            @foreach($arrFirst as $a)
                                                                @if($mainArr->has($a) && in_array($f->id, $arr))
                                                                    <span class="relations-block__value-relation js-value-relation">
                                                                        {{$mainArr[$a]->name ? $mainArr[$a]->name : $mainArr[$a]->value}}
                                                                     </span>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </span>
                                            </label>
                                        @else
                                            <label class="relations-block__wrap-checkbox js-label-checkbox">
                                                <input type="checkbox" class="relations-block__checkbox js-checkbox"
                                                    name='f-{{$f->id}}-2'
                                                    id="f-{{trim($f->id)}}-2"
                                                    value='{"is_first":0,"is_feature":1,"value":"{{$f->id}}"}'
                                                    data-value="{{$f->id}}"
                                                    data-name="{{trim($f->name)}}"
                                                >
                                                <span class="relations-block__value">{{$f->name}}</span>
                                                <span class="relations-block__wrap-relation js-wrap-relation">
                                                    @foreach($theseRelations as $r)
                                                        @php
                                                            $valueFirst = $r->values_first ? $r->values_first : $r->features_first;
                                                            $valueSecond = $r->values_second ? $r->values_second : $r->features_second;
                                                            $mainArr = $r->values_first ? $valuesFirstAll : $featuresFirstAll;
                                                            $arr = [];
                                                            $arrFirst = explode(',', $valueFirst);
                                                            $arr = explode(',', $valueSecond);
                                                        @endphp
                                                        @if(!empty($arrFirst))
                                                            @foreach($arrFirst as $a)
                                                                @if($mainArr->has($a) && in_array($f->id, $arr))
                                                                    <span class="relations-block__value-relation js-value-relation">
                                                                        {{$mainArr[$a]->name ? $mainArr[$a]->name : $mainArr[$a]->value}}
                                                                     </span>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                     {{--@foreach($theseRelations as $r)--}}
                                                        {{--@php--}}
                                                            {{--$arr = [];--}}
                                                            {{--if(!is_null($r->features_second)) {--}}
                                                                {{--$arr = explode(',', $r->features_second);--}}
                                                            {{--}--}}
                                                        {{--@endphp--}}
                                                        {{--@isset($r['features_first'])--}}
                                                            {{--@if($featuresFirstAll->firstWhere('id', $r['features_first']) && in_array($f->id, $arr))--}}
                                                                {{--<span class="relations-block__value-relation js-value-relation">--}}
                                                                    {{--{{$featuresFirstAll[$r['features_first']]->name}}--}}
                                                                 {{--</span>--}}
                                                            {{--@endif--}}
                                                        {{--@endisset--}}
                                                    {{--@endforeach--}}
                                                </span>
                                            </label>
                                        @endif
                                    @endforeach
                                @endif
                            </fieldset>
                        </div>
                        <div class="col-md-8">
                            <button type="submit" class="relations-block js-save-relation btn btn-success">Редактировать связь</button>
                        </div>
                    </div>
                    <input type="hidden" name="relation-id" id="relation-id" value="{{$id}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                </form>
                <a href="/admin/relations" class="relations-block__back btn btn-dark">Назад</a>
            </div>
        </div>
    @endif

    @if(false)
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                    <form role="form"
                          class="form-edit-add"
                          action="{{ $edit ? route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) : route('voyager.'.$dataType->slug.'.store') }}"
                          method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                    @if($edit)
                        {{ method_field("PUT") }}
                    @endif

                    <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        <!-- Adding / Editing -->
                            @php
                                $dataTypeRows = $dataType->{($edit ? 'editRows' : 'addRows' )};
                            @endphp

                            @foreach($dataTypeRows as $row)
                            <!-- GET THE DISPLAY OPTIONS -->
                                @php
                                    $display_options = $row->details->display ?? NULL;
                                    if ($dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')}) {
                                        $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_'.($edit ? 'edit' : 'add')};
                                    }
                                @endphp
                                @if (isset($row->details->legend) && isset($row->details->legend->text))
                                    <legend class="text-{{ $row->details->legend->align ?? 'center' }}" style="background-color: {{ $row->details->legend->bgcolor ?? '#f0f0f0' }};padding: 5px;">{{ $row->details->legend->text }}</legend>
                                @endif

                                <div class="form-group @if($row->type == 'hidden') hidden @endif col-md-{{ $display_options->width ?? 12 }} {{ $errors->has($row->field) ? 'has-error' : '' }}" @if(isset($display_options->id)){{ "id=$display_options->id" }}@endif>
                                    {{ $row->slugify }}
                                    <label class="control-label" for="name">{{ $row->getTranslatedAttribute('display_name') }}</label>
                                    @include('voyager::multilingual.input-hidden-bread-edit-add')
                                    @if (isset($row->details->view))
                                        @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => ($edit ? 'edit' : 'add'), 'view' => ($edit ? 'edit' : 'add'), 'options' => $row->details])
                                    @elseif ($row->type == 'relationship')
                                        @include('voyager::formfields.relationship', ['options' => $row->details])
                                    @else
                                        {!! app('voyager')->formField($row, $dataType, $dataTypeContent) !!}
                                    @endif

                                    @foreach (app('voyager')->afterFormFields($row, $dataType, $dataTypeContent) as $after)
                                        {!! $after->handle($row, $dataType, $dataTypeContent) !!}
                                    @endforeach
                                    @if ($errors->has($row->field))
                                        @foreach ($errors->get($row->field) as $error)
                                            <span class="help-block">{{ $error }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach

                        </div><!-- panel-body -->

                        <div class="panel-footer">
                            @section('submit-buttons')
                                <button type="submit" class="btn btn-primary save">{{ __('voyager::generic.save') }}</button>
                            @stop
                            @yield('submit-buttons')
                        </div>
                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                          enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file"
                               onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}</h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="pop-up pop-up_save-relation" tabindex="-1" id="pop-up-save-relation" role="dialog">
        <div class="pop-up-content">
            <div class="pop-up-body">
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop

@section('javascript')
    <script>
        var params = {};
        var $file;

        //Записываем все связи в хранилище
        // localStorage.removeItem('pc-config');
        localStorage.setItem('pc-config', "{{$relations}}");
        @isset($theseRelations)
                localStorage.setItem('these-relations', "{{$theseRelations}}");
        @endisset

        function deleteHandler(tag, isMulti) {
            return function() {
                $file = $(this).siblings(tag);

                params = {
                    slug:   '{{ $dataType->slug }}',
                    filename:  $file.data('file-name'),
                    id:     $file.data('id'),
                    field:  $file.parent().data('field-name'),
                    multi: isMulti,
                    _token: '{{ csrf_token() }}'
                }

                $('.confirm_delete_name').text(params.filename);
                $('#confirm_delete_modal').modal('show');
            };
        }

        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function (idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: [ 'YYYY-MM-DD' ]
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
            $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function(){
                $.post('{{ route('voyager.'.$dataType->slug.'.media.remove') }}', params, function (response) {
                    if ( response
                        && response.data
                        && response.data.status
                        && response.data.status == 200 ) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() { $(this).remove(); })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
