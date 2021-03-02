<div class="panel panel_categories">
    <div class="panel__content">
        <div class="panel__header">
            <div class="panel__title">Выберите категорию</div>
            <button class="panel__close js-close-panel-category" type="button" aria-label="Закрыть панель выбора категорий">Х</button>
        </div>
        <div class="panel__body">
            @foreach($sectionsAll as $k => $var)
                <div class="categories-list__my-pc-link">
                    <a class="categories-link" href="/your-pc">Мой компьютер</a>
                </div>
                <div class="divider">{{$k ? 'Обязательные' : 'Оптимальные'}}</div>
                <ul class="list list_unstyled categories-list">
                    @foreach($var as $s)
                        @if($configuration && $configuration->has($s->id))
                            <li class="list-item">
                                <span class="panel__link_blocked">{{$s->name}}</span>
                                <button class="categories-clean" type="button" aria-label="очистить">X</button>
                            </li>
                        @else
                            <li class="list-item">
                                <a class="categories-link" href="{{$s->url}}">{{$s->name}}</a>
                                <button class="categories-clean" type="button" aria-label="очистить">X</button>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>
</div>
