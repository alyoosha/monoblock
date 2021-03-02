@foreach($theseRelations as $r)
    @if(isset($r['selected']))
        <option
            value="{{$r->id}}"
            selected
        >{{$r->name_option}}
        </option>
    @else
        <option
                value="{{$r->id}}"
        >{{$r->name_option}}
        </option>
    @endif
@endforeach