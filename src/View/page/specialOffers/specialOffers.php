<div class="main__container">
	<section class="form">
		<?php $this->getVariable('form')->display() ?>
	</section>
	<section class="basket">
		<?php $this->getVariable('basket')->display() ?>
	</section>
	<section class="special__offer">
		<ul class="special__offer__list">
			<?php foreach ($this->getVariable('specialOffers') as $specialOffer): ?>
				<?= $specialOffer->display() ?>
			<?php endforeach; ?>
		</ul>
	</section>
</div>