<tr class="table__tr">
	<td id="productId" class="table__th table__th_id"><?= $this->getVariable('userId') ?></td>
	<td id="productTitle" class="table__th table__th_title"><?= $this->getVariable('userId') ?></td>
	<td id="productDesc" class="table__th table__th_desc"><?= $this->getVariable('address') ?></td>
	<td id="productPrice" class="table__th table__th_price"><?= $this->getVariable('statusId') ?></td>
	<td class="table__th"><?= date("m-d-Y H:i:s", $this->getVariable('createdAt'))?></td>
	<td class="table__th"><?= $this->getVariable('name') ?></td>
	<td class="table__th"><?= $this->getVariable('surname') ?></td>
	<td class="table__th table__th_button">
		<button class="table__button table__button_edit">Редактировать</button>
		<button class="table__button table__button_delete">Удалить</button>
	</td>
</tr>