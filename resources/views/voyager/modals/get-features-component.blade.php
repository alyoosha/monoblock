@if(!empty($components))
    <div class="modal-header">
        <h3>
            {{$components->first()->full_name}}
        </h3>
    </div>
    <div class="modal-body">
        <div class="modal-body__box">
            @foreach($components as $k => $c)
                <div class="modal-body__title">{{$c->feature_type_custom_name ? $feature_type_custom_name : $c->feature_type_name }}:</div>
                @isset($c->feature_name)
                    <div class="modal-body__text">{{$c->feature_name}}</div>
                @endisset
                @isset($c->value)
                    @if($c->filter_type == 'boolean')
                        <div class="modal-body__text">{{$c->value == 1 ? 'Есть' : 'Нет'}}</div>
                    @else
                        <div class="modal-body__text">{{$c->value}}</div>
                    @endif
                @endisset
            @endforeach
        </div>
    </div>
@else
    <div class="modal-body">
        <h4>{{ __('components.no_features', [], $lang) }}</h4>
    </div>
@endif
