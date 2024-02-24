<div class="main__container">
	<section class="form">
		<?php
		$this->getVariable('form')->display() ?>
	</section>
	<section class="basket">
		<?php
		$this->getVariable('basket')->display() ?>
	</section>
	<section class="product">
		<h1 class="special_offer_title"><?= $this->getVariable('specialOfferTitle') ?></h1>
		<ul class="product__list">
			<?php
			foreach ($this->getVariable('products') as $product): ?>
				<?php
				$product->display() ?>
			<?php
			endforeach; ?>
		</ul>
		<?php
		$this->getVariable('pagination')->display() ?>
	</section>
</div>