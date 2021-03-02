<div class="row justify-content-between align-items-center">
    @if(!$isHome)
        <button class="main-menu js-open-menu" type="button">Меню</button>
    @endif
    <div class="main-logo">
        <a href="/" class="main-logo__link">
            <span class="main-logo__title line">21vek.by</span>
            <span class="main-logo__descr line">онлайн-гипермаркет</span>
        </a>
    </div>
    @if(!$isHome && !$hasFilter)
        <button class="main-add" type="button">Добавить</button>
    @endif
    @if($hasFilter)
        <button class="main-add js-open-filter" type="button">Фильтр</button>
    @endif
</div>
