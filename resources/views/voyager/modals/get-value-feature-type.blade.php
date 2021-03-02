@if(!$sections->isEmpty())
    <div class="modal-header">
        <h3>
            {{$section}}
        </h3>
    </div>
    <div class="modal-body">
        @foreach($sections as $k => $s)
            @if($sections->last())
                <h4>{{$k}}</h4>
                <ol>
                    @foreach($s as $ss)
                        <li>
                            <div class="wrap">
                                <span>{{$ss->name}}</span>
                                <span class="quantity">{{$ss->total}}</span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @else
                <h4>{{$k}}</h4>
                <ol>
                    @foreach($s as $key => $ss)
                        <li>
                            <div class="wrap">
                                <span>{{$key}}</span>
                                <span class="quantity">
                                    @if(isset($ss['total']))
                                        {{$ss['total']}}
                                    @endif
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
            @break
        @endforeach
    </div>
@else
    <div class="modal-body">
        <h4>{{ __('feature-types.no_values', [], $lang) }}</h4>
    </div>
@endif
