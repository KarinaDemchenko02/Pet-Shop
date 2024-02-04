
<div class="main__container container">
    <section class="details">
        <div class="details_main-information">
            <div id="img-container" class="details__images-container">
                <img id="product" class="details__images" src="/../images/productImages.png" alt="product">
            </div>
            <div class="details__information">
                <div class="details__name-container">
                    <h2 class="details__name"><?= $this->getVariable('title') ?></h2>
                    <a class="details__more" href="#details">Подробности</a>
                </div>
                <div class="details__price-container">
                    <h2 class="details__price"><?= $this->getVariable('price') ?> ₽ /шт</h2>
                </div>
                <div class="details__btn-buy">
                    <button class="details__add-basket">В корзину</button>
                    <button class="details__buy">Купить</button>
                </div>
            </div>
        </div>
        <ul class="details__list">
            <li class="details__item" >
                <button class="details__btn-item is-active" data-tab="#description">Описание</button>
            </li>
            <li class="details__item">
                <button class="details__btn-item" data-tab="#specifications">Характеристики</button>
            </li>
        </ul>
        <div id="details" class="details__read-information">
            <div id="description" class="details__info-items details__description">
                <p class="details__description-more">
                    <span class="details__span-description"><?= $this->getVariable('desc') ?></span>
                </p>
            </div>
            <div id="specifications" class="details__info-items details__specifications">
                <ul class="details__list-specifications">
                    <li class="details__item-specifications">
                        <span class="details__specifications-name">Возраст</span>
                        <span class="details__ellipsis"></span>
                        <span class="details__characteristic">Взрослое Животное</span>
                    </li>
                    <li class="details__item-specifications">
                        <span class="details__specifications-name">Производитель</span>
                        <span class="details__ellipsis"></span>
                        <span class="details__characteristic">Premier</span>
                    </li>
                    <li class="details__item-specifications">
                        <span class="details__specifications-name">Страна производства</span>
                        <span class="details__ellipsis"></span>
                        <span class="details__characteristic">Бельгия</span>
                    </li>
                    <li class="details__item-specifications">
                        <span class="details__specifications-name">Категория товара</span>
                        <span class="details__ellipsis"></span>
                        <span class="details__characteristic">Сухой корм</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>
