<div class="delete">
	<p class="delete__question">Вы действительно хотите удалить элемент?</p>
	<div class="delete__form-container">
		<form class="delete__form" method="post">
			<input type="hidden" name="action" value="disable">
			<input id="idProductDisable" class="form__input" name="id" type="hidden" readonly>
			<button class="delete__button delete__button-yes" type="submit" name="disable">Удалить</button>
		</form>
		<button class="delete__button delete__button-no">Отмена</button>
	</div>
</div>