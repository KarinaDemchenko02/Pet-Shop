<div class="main__container container">
	<section class="details">
		<div class="details_main-information">
			<div id="img-container" class="details__images-container">
				<img id="product" class="details__images" src="<?=$this->getVariable('imagePath')?>" alt="product">
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
			<li class="details__item">
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
					<?php
					foreach ($this->getVariable('characteristics') as $characteristic): ?>
						<li class="details__item-specifications">
							<span class="details__specifications-name"><?= $characteristic->title ?></span>
							<span class="details__ellipsis"></span>
							<span class="details__characteristic"><?= $characteristic->value ?></span>
						</li>
					<?php
					endforeach; ?>
				</ul>
			</div>
		</div>
	</section>
	<section class="form" id="form-auth-detail"></section>
	<section class="form-product" id="form-product"></section>
	<section class="basket">
		<?php $this->getVariable('basket')->display(); ?>
	</section>
	<section class="success" id="success">
		<div class="success__container">
			<h2 class="success__heading">Заказ создан! С вами свяжется оператор.</h2>
			<a href="/" class="success__button">Перейти на главную страницу</a>
		</div>
	</section>
</div>
<script type="module">
	import { Order } from "/js/main/order/order.js";
	import {Auth} from "/js/main/auth/auth.js";

	const mainList = new Order({
		attachToNodeId: 'form-product',
		id: <?= \Up\Util\Json::encode($this->getVariable('id')) ?>,
		title: <?= \Up\Util\Json::encode($this->getVariable('title')) ?>,
		price: <?= \Up\Util\Json::encode($this->getVariable('price')) ?>,
		imagePath: <?= \Up\Util\Json::encode($this->getVariable('imagePath'))?>
	});
	mainList.render();

	const auth = new Auth({
		attachToNodeId: 'form-auth-detail',
		login: <?= \Up\Util\Json::encode($this->getVariable('isLogin')) ?>,
	})

	auth.render();
</script>
