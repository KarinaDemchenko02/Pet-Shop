<table class="table">
	<h2 class="table__heading"><?= $this->getVariable('title') ?></h2>
	<button class="table__button-add">Добавить</button>
	<tr class="table__tr">
		<?php foreach ($this->getVariable('columns') as $columns): ?>
			<th class="table__th table__th-heading"><?= $columns ?></th>
		<?php endforeach; ?>
		<th class="table__th table__th-heading">действие</th>
	</tr>
	<?php foreach ($this->getVariable('data') as $el): ?>
		<?= $el->display() ?>
	<?php endforeach; ?>
</table>