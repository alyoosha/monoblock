@extends('voyager::master')

@section('content')
    <div class="page-content read container-fluid">
        @foreach($relations as $relation)
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-bordered" style="padding-bottom:5px;">
                        <div class="panel-heading">
                            <h3 class="panel-title">ID</h3>
                        </div>
                        <div class="panel-body">
                            {{$relation->id }}
                        </div>
                        @foreach($dataType->readRows as $row)
                            @if($row->field == 'features_first' && !is_null($relation->{$row->field}))
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{$row->display_name}}</h3>
                                </div>
                                <div class="panel-body">
                                    @if(is_array($relation->{$row->field}))
                                        {{ implode(', ',$relation->{$row->field})}}
                                    @else
                                        {{ $relation->{$row->field} }}
                                    @endif
                                </div>
                            @elseif($row->field == 'features_second' && !is_null($relation->{$row->field}))
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{$row->display_name}}</h3>
                                </div>
                                <div class="panel-body">
                                    @if(is_array($relation->{$row->field}))
                                        {{ implode(', ',$relation->{$row->field})}}
                                    @else
                                        {{ $relation->{$row->field} }}
                                    @endif
                                </div>
                            @elseif($row->field == 'values_first' && !is_null($relation->{$row->field}))
                                @php
                                @endphp
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{$row->display_name}}</h3>
                                </div>
                                <div class="panel-body">
                                    @if(is_array($relation->{$row->field}))
                                        {{ implode(', ',$relation->{$row->field})}}
                                    @else
                                        {{ $relation->{$row->field} }}
                                    @endif
                                </div>
                            @elseif($row->field == 'values_second' && !is_null($relation->{$row->field}))
                                @php
                                @endphp
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{$row->display_name}}</h3>
                                </div>
                                <div class="panel-body">
                                    @if(is_array($relation->{$row->field}))
                                        {{ implode(', ',$relation->{$row->field})}}
                                    @else
                                        {{ $relation->{$row->field} }}
                                    @endif
                                </div>
                            @elseif(!is_null($relation->{$row->field}))
                                <div class="panel-heading">
                                    <h3 class="panel-title">{{$row->display_name}}</h3>
                                </div>
                                <div class="panel-body">
                                    {{ $relation->{$row->field} }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
            <a href="/admin/relations" class="relations-block__back btn btn-dark">Назад</a>
    </div>
@stop
