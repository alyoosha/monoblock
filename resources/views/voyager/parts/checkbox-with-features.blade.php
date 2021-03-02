@if(isset($features['values']))
    @if($features['filter'] == 'list')
        @foreach($features['values'] as $k => $f)
            <label class="relations-block__wrap-checkbox js-label-checkbox">
                <input type="checkbox" class="relations-block__checkbox js-checkbox"
                    name='{{$isFirst == 1 ? 'f-' . trim($f['name']) . '-1' : 'f-' . trim($f['name']) . '-2'}}'
                    id='{{$isFirst == 1 ? 'f-' . trim($f['name']) . '-1' : 'f-' . trim($f['name']) . '-2'}}'
                    value='{"is_first":{{$isFirst == 1 ? 1 : 0}},"value": "{{$f['id']}}"}'
                    data-value="{{$f['id']}}"
                    data-name="{{trim($f['name'])}}"
                >
                <span class="relations-block__value">{{trim($f['name'])}}</span>
                @if(!$isFirst)
                    <span class="relations-block__wrap-relation js-wrap-relation">
                        <span class="relations-block__value-relation js-value-relation"></span>
                    </span>
                @endif
            </label>
        @endforeach
    @elseif($features['filter'] == 'number')
        @foreach($features['values'] as $k => $v)
            <label class="relations-block__wrap-checkbox js-label-checkbox">
                <input type="checkbox" class="relations-block__checkbox js-checkbox"
                    name='{{$isFirst == 1 ? 'v-' . trim($k ). '-1' : 'v-' . trim($k ). '-2'}}'
                    id='{{$isFirst == 1 ? 'v-' . trim($k ). '-1' : 'v-' . trim($k ). '-2'}}'
                    value='{"is_first":{{$isFirst == 1 ? 1 : 0}},"value":"{{$k}}"}'
                    data-value="{{trim($k)}}"
                    data-name="{{trim($k)}}"
                >
                <span class="relations-block__value">{{trim($k)}}</span>
                @if(!$isFirst)
                    <span class="relations-block__wrap-relation js-wrap-relation">
                        <span class="relations-block__value-relation js-value-relation"></span>
                    </span>
                @endif
            </label>
        @endforeach
    @elseif($features['filter'] == 'string')
        @foreach($features['values'] as $k => $v)
            <label class="relations-block__wrap-checkbox js-label-checkbox">
                <input type="checkbox" class="relations-block__checkbox js-checkbox"
                    name='{{$isFirst == 1 ? 'v-' . trim($k ). '-1' : 'v-' . trim($k ). '-2'}}'
                    id='{{$isFirst == 1 ? 'v-' . trim($k ). '-1' : 'v-' . trim($k ). '-2'}}'
                    value='{"is_first":{{$isFirst == 1 ? 1 : 0}},"value":"{{trim($k)}}"}'
                    data-value="{{trim($k)}}"
                    data-name="{{trim($k)}}"
                >
                <span class="relations-block__value">{{trim($k)}}</span>
                @if(!$isFirst)
                    <span class="relations-block__wrap-relation js-wrap-relation">
                        <span class="relations-block__value-relation js-value-relation"></span>
                    </span>
                @endif
            </label>
        @endforeach
    @elseif($features['filter'] == 'boolean')
        @foreach($features['values'] as $k => $f)
            <label class="relations-block__wrap-checkbox js-label-checkbox">
                <input type="checkbox" class="relations-block__checkbox js-checkbox"
                    name='{{$isFirst == 1 ? 'v-boolean-1' : 'v-boolean-2'}}'
                    value='{"is_first":{{$isFirst == 1 ? 1 : 0}},"value":"{{trim($k)}}"}'
                    data-value="{{trim($k)}}"
                    data-name="{{trim($k)}}"
                >
                <span class="relations-block__value">{{trim($k)}}</span>
                @if(!$isFirst)
                    <span class="relations-block__wrap-relation js-wrap-relation">
                        <span class="relations-block__value-relation js-value-relation"></span>
                    </span>
                @endif
            </label>
        @endforeach
    @endif
@endif

