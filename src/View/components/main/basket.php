<div class="basket__container">
	<button class="basket__button-close">
		<i class="basket__icon-close material-icons">close</i>
	</button>
	<h2 class="basket__heading">Корзина</h2>
	<ul class="basket__list" id="basket-list"></ul>
	<button class="basket__buy">Купить</button>
</div>

<script type="module">
	import { BasketList } from "/js/main/basket/basket-list.js";

	const basketList = new BasketList({
		attachToNodeId: 'basket-list',
		items: <?= \Up\Util\Json::encode($this->getVariable('items')) ?>,
	});

	basketList.render();
</script>
