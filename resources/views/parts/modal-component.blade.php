 <div class="panel__body">
     <div class="product-title">{{$components->first()->full_name}}</div>
     <div class="product-img">
         <img src="/images/content/videocard.png" alt="{{$components->first()->full_name}}">
     </div>
     <div class="product-descr">{{$components->first()->full_name}}</div>
     <div class="product-params">
         <table class="table">
             <tbody>
             @foreach($components as $component)
                 <tr>
                     <th>{{$component->feature_type_name}}</th>
                     @if($component->filter_type != 'boolean')
                         <td>{{$component->feature_name ? $component->feature_name : $component->value}}</td>
                     @else
                         <td>{{$component->value ? 'Есть' : 'Нет'}}</td>
                     @endif
                 </tr>
             @endforeach
             </tbody>
         </table>
     </div>
 </div>
 <div class="panel__footer">
     <div class="row justify-content-center align-items-center">
         <div class="col-auto">
             <button class="product-remove" type="button">X</button>
         </div>
         <div class="col-auto">
             <div class="product-price">
                 <span class="count">{{$components->first()->price}}</span>
                 <span class="currency">р.</span>
             </div>
         </div>
         <div class="col-auto">
             <button class="product-add" type="button">Ок</button>
         </div>
     </div>
 </div>