<div class="form__container">
	<button class="form__close">
		<i class="form__close-icon material-icons">close</i>
	</button>
	<form id="form" class="form" method="post" action="/admin/action/">

		<input id="action" class="form__input-action" type="hidden" name="action" value="">
		<input id="idProduct" value="" class="form__input" name="id" type="hidden" readonly>

		<label class="form__label" for="title">Название</label>
		<input id="title" value="" class="form__input" name="title" type="text">

		<label class="form__label" for="desc">Описание</label>
		<input id="desc" value="" class="form__input" name="desc" type="text">

		<label class="form__label" for="price">Цена</label>
		<input id="price" value="" class="form__input" name="price" type="text">

		<label class="form__label" for="tag">Тег</label>
		<input id="tag" value="" class="form__input" name="tag" type="text">

		<button id="changed" class="form__button form__button_change" name="changeProduct" type="submit">Редактировать</button>
		<button class="form__button form__button_add" name="add" type="submit">Добавить</button>
	</form>
	<div id="error"></div>
	<div id="information"></div>
</div>
