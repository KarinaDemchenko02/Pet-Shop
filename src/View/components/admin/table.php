<div class="table__container">
	<h2 class="table__heading">Товары</h2>
	<button class="table__button-add">Добавить</button>
	<table class="table">
		<tr class="table__tr">
			<?php foreach ($this->getVariable('columnsProducts') as $columns): ?>
				<th class="table__th table__th-heading"><?= $columns ?></th>
			<?php endforeach; ?>
			<th class="table__th table__th-heading">действие</th>
		</tr>
		<?php foreach ($this->getVariable('products') as $product): ?>
			<?= $product->display() ?>
		<?php endforeach; ?>
	</table>
	<div class="form__box">
		<?php $this->getVariable('form')->display(); ?>
	</div>
	<div class="delete__box">
		<?php $this->getVariable('delete')->display(); ?>
	</div>
</div>
