@extends('voyager::master')

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel destroy panel-bordered" style="padding-bottom:5px;">
                    <div class="relations-block">
                        <h4>Удалить связи: </h4>
                        <form action="/api/relations/remove-relations"
                              class="relations-block__form" name="form-remove-relations"
                              method="post"
                              enctype="multipart/form-data"
                        >
                            @foreach($theseRelations as $relation)
                                <label class="relations-block__wrap-checkbox relations-block__wrap-checkbox_remove">
                                    @if($relation->id == $id)
                                        <input
                                            type="checkbox"
                                            class="relations-block__checkbox"
                                            name="relation-{{$relation->id}}"
                                            value="{{$relation->id}}"
                                            checked
                                        />
                                            <span class="relations-block__value">{{$relation->name_option}}</span>
                                        @else
                                        <input
                                            type="checkbox"
                                            class="relations-block__checkbox"
                                            value="{{$relation->id}}"
                                            name="relation-{{$relation->id}}"
                                        />
                                            <span class="relations-block__value">{{$relation->name_option}}</span>
                                     @endif
                                </label>
                            @endforeach
                            <button title="Удалить" class="btn btn-sm btn-danger delete line">
                                <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Удалить</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <a href="/admin/relations" class="relations-block__back btn btn-dark">Назад</a>
    </div>
@endsection
