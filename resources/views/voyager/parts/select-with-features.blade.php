<option value="">Выберете характеристику</option>
@if(isset($features['values']))
    @if($features['filter'] == 'list')
        @foreach($features['values'] as $k => $f)
            <option
                value='{"is_first":{{$isFirst == 1 ? 1 : 0}},"value": "{{$f['id']}}"}'
                data-value="{{$f->id}}"
                data-name="{{$f->name}}"
            >{{$f->name}}
            </option>
        @endforeach
    @elseif($features['filter'] == 'string')
        @foreach($features['values'] as $k => $v)
            <option
                value='{"is_first":{{$isFirst == 1 ? 1 : 0}},"value": "{{$k}}"}'
                data-value="{{$k}}"
                data-name="{{$k}}"
            >{{$k}}
            </option>
        @endforeach
    @elseif($features['filter'] == 'number')
        @foreach($features['values'] as $k => $v)
            <option
                value='{"is_first":{{$isFirst == 1 ? 1 : 0}},"value": "{{$k}}"'
                data-value="{{$k}}"
                data-name="{{$k}}"
            >{{$k}}
            </option>
        @endforeach
    @elseif($features['filter'] == 'boolean')
        @foreach($features['values'] as $k => $f)
            <option
                value='{"is_first":{{$isFirst == 1 ? 1 : 0}},"value": "{{$f}}"'
                data-value="{{$f}}"
                data-name="{{$f}}"
            >{{$f}}
            </option>
        @endforeach
    @endif
@endif

