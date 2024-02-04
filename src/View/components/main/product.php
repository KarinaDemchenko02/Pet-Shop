<li class="product__wrapper">
        <div class="product__content">
            <a href="/product/<?=$this->getVariable('id')?>/">
                <img class="product__images" src="/../images/productImages.png" alt="product">
            </a>
            <div class="product__bottom-content">
                <div class="product__left-content">
                    <div class="product__details">
                        <h2 class="product__heading"><?=$this->getVariable('title')?></h2>
                        <p class="product__price"><?=$this->getVariable('price')?> ₽</p>
                    </div>
                    <button class="product__buy">
                        <i class="material-icons product__icon-buy">add_shopping_cart</i>
                    </button>
                </div>
                <div class="product__right-content">
                    <div class="product__right-done">
                        <i class="material-icons product__done-icon">done</i>
                    </div>
                    <div class="product__details">
                        <h2 class="product__heading product__heading-details"><?=$this->getVariable('title')?></h2>
                        <p class="product__price product__price-details">Товар в корзине</p>
                    </div>
                    <div class="product__right-remove">
                        <i class="material-icons product__remove-icon">clear</i>
                    </div>
                </div>
            </div>
        </div>
        <div class="product__information">
            <div class="product__container-icon">
                <i class="product__icon material-icons">info_outline</i>
            </div>
            <div class="product__more-content">
                <p class="product__info">
                    <?=$this->getVariable('desc')?>
                </p>
            </div>
        </div>
</li>
