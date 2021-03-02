<option value="">Выберете характеристику</option>

@foreach($featureTypes as $f)
    <option
        value="{{$f->id}}"
        data-filter="{{$f->filter_type}}"
    >{{$f->custom_name ? $f->custom_name : $f->name}}
    </option>
@endforeach