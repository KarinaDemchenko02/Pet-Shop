<li class="special_offer_card">
	<a href="/special-offer/<?=$this->getVariable('id')?>/">
		<div class="special_offer__info">
			<h1><?=$this->getVariable('title')?></h1>
			<p>Дата начала акции: <?=$this->getVariable('startDate')?></p>
			<p>Дата конца акции: <?=$this->getVariable('endDate')?></p>
			<p><?=$this->getVariable('description')?></p>
		</div>
		<ul class="product__list">
			<?php
			foreach ($this->getVariable('products') as $product): ?>
				<?php
				$product->display() ?>
			<?php
			endforeach; ?>
		</ul>
	</a>
</li>