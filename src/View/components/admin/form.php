<div class="form__container">
	<button class="form__close">
		<i class="form__close-icon material-icons">close</i>
	</button>
	<div id="form" class="form">
		<label class="form__label" for="title">Название</label>
		<input id="title" class="form__input" name="title" type="text">

		<label class="form__label" for="desc">Описание</label>
		<input id="desc" class="form__input" name="desc" type="text">

		<label class="form__label" for="price">Цена</label>
		<input id="price" class="form__input" name="price" type="text">

		<label class="form__label" for="tags">Теги</label>
		<input id="tags" class="form__input" name="tags" type="text">

		<button id="changed" class="form__button form__button_change" name="changeProduct" type="submit">Редактировать</button>
		<button class="form__button form__button_add" name="add" type="submit">Добавить</button>
	</div>
	<div id="error"></div>
	<div id="information"></div>
</div>
