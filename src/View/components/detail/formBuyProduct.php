<div class="form-product__container">
    <div class="form-product__modal">
        <div class="form-product__item">
            <img class="form-product__images" src="/../images/productImages.png" alt="product">
            <h2 class="form-product__heading"><?=$this->getVariable('title')?></h2>
            <p class="form-product__price"><?=$this->getVariable('price')?> ₽</p>
        </div>
        <div class="form-product__round"></div>
    </div>
    <div class="form-product__container-info">
        <div class="form-product__info">
            <h2 class="form-product__info-heading">Оформление заказа</h2>
        </div>
        <form class="form-product__main-form">
            <label for="name">Имя</label>
        </form>
    </div>
</div>