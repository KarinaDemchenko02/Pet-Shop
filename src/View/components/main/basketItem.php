<li class="basket__item">
	<form method="post" action="/deleteFromBasket/<?= $this->getVariable('id') ?>/">
	<button class="basket__delete">
		<i class="basket__delete-icon material-icons">close</i>
	</button>
	</form>
	<img class="basket__images" src="<?=$this->getVariable('imagePath')?>" alt="product">
	<h2 class="basket__heading-product"><?=$this->getVariable('title')?></h2>
	<div class="basket__quantity">
		<button class="basket__btn-quantity basket__plus-btn">
			<i class="basket__plus-icon material-icons">add</i>
		</button>
		<input id="1" class="basket__input-number" type="text" name="name" value="<?=$this->getVariable('quantity')?>">
		<button class="basket__btn-quantity basket__minus-btn">
			<i class="basket__minus-icon material-icons">remove</i>
		</button>
	</div>
	<span class="basket__price"><?=$this->getVariable('price')?>₽</span>
</li>
