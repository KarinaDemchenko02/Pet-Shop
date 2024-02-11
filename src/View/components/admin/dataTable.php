<tr class="table__tr">
	<td id="productId" class="table__th table__th_id"><?= $this->getVariable('id') ?></td>
	<td id="productTitle" class="table__th table__th_title"><?= $this->getVariable('title') ?></td>
	<td id="productDesc" class="table__th table__th_desc"><?= $this->getVariable('desc') ?></td>
	<td id="productPrice" class="table__th table__th_price"><?= $this->getVariable('price') ?></td>
	<td class="table__th"><?= date("m-d-Y H:i:s", $this->getVariable('addedAt')) ?></td>
	<td class="table__th"><?= date("m-d-Y H:i:s", $this->getVariable('editedAt')) ?></td>
	<td class="table__th"><?= $this->getVariable('isActive') ?></td>
	<td class="table__th table__th_button">
		<button class="table__button table__button_edit">Редактировать</button>
		<button class="table__button table__button_delete">Удалить</button>
	</td>
</tr>

