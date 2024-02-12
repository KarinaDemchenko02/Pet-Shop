<div class="basket__container">
	<button class="basket__button-close">
		<i class="basket__icon-close material-icons">close</i>
	</button>
	<h2 class="basket__heading">Корзина</h2>
	<ul class="basket__list">
		<?php foreach ($this->getVariable('items') as $item) {$item->display();} ?>
	</ul>
</div>
