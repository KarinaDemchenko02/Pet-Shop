export class ProductItem
{
	id;
	title;
	description;
	price;
	tags;
	priority;
	imagePath;
	addedAt;
	editedAt;
	isActive;
	editButtonHandler;
	removeButtonHandler;
	restoreButtonHandler;
	addImageButtonHandler;

	constructor({ id, title, description, tags, price, imagePath,addedAt, editedAt, isActive, priority, editButtonHandler, removeButtonHandler, restoreButtonHandler, addImageButtonHandler })
	{
		this.id = Number(id);
		this.title = String(title);
		this.description = String(description);
		this.price = Number(price);
		this.imagePath = String(imagePath);
		this.addedAt = this.renderDate(addedAt);
		this.editedAt = this.renderDate(editedAt);
		this.isActive = Boolean(isActive);
		this.priority = Number(priority);
		this.tags = tags;

		if (typeof editButtonHandler === 'function')
		{
			this.editButtonHandler = editButtonHandler;
		}

		if (typeof removeButtonHandler === 'function')
		{
			this.removeButtonHandler = removeButtonHandler;
		}

		if (typeof restoreButtonHandler === 'function')
		{
			this.restoreButtonHandler = restoreButtonHandler;
		}

		if (typeof addImageButtonHandler === "function")
		{
			this.addImageButtonHandler = addImageButtonHandler;
		}
	}

	render()
	{
		const trProduct = document.createElement('tr');
		trProduct.id = String(this.id) + 'tr';
		trProduct.classList.add('table__tr');

		const idColumn = document.createElement('td');
		idColumn.classList.add('table__th', 'table__th_id');
		idColumn.innerText = this.id;

		const titleColumn = document.createElement('td');
		titleColumn.classList.add('table__th', 'table__th_title');
		titleColumn.innerText = this.title;

		const descColumn = document.createElement('td');
		descColumn.classList.add('table__th', 'table__th_desc');
		descColumn.innerText = this.description;

		const priceColumn = document.createElement('td');
		priceColumn.classList.add('table__th', 'table__th_price');
		priceColumn.innerText = this.price;

		const addedAtColumn = document.createElement('td');
		addedAtColumn.classList.add('table__th');
		addedAtColumn.innerText = this.addedAt;

		const editedAtColumn = document.createElement('td');
		editedAtColumn.classList.add('table__th');
		editedAtColumn.innerText = this.editedAt;

		const isActiveColumn = document.createElement('td');
		isActiveColumn.classList.add('table__th');
		isActiveColumn.innerText = this.isActive;

		const priorityColumn = document.createElement('td');
		priorityColumn.classList.add('table__th');
		priorityColumn.innerText = this.priority;

		const tagsColumn = this.createTagColumn();
		tagsColumn.classList.add('table__th', 'table__th_tags');

		const spinnerRemove = document.createElement('div');
		spinnerRemove.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingRemove = document.createElement('span');
		spinnerLoadingRemove.innerText = 'Loading...';
		spinnerLoadingRemove.classList.add('visually-hidden');
		spinnerRemove.append(spinnerLoadingRemove);

		const spinnerRestore = document.createElement('div');
		spinnerRestore.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingRestore = document.createElement('span');
		spinnerLoadingRestore.innerText = 'Loading...';
		spinnerLoadingRestore.classList.add('visually-hidden');
		spinnerRestore.append(spinnerLoadingRestore);

		const spinnerEdit = document.createElement('div');
		spinnerEdit.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingEdit = document.createElement('span');
		spinnerLoadingEdit.innerText = 'Loading...';
		spinnerLoadingEdit.classList.add('visually-hidden');
		spinnerEdit.append(spinnerLoadingEdit);

		const removeButton = document.createElement('button');
		removeButton.classList.add('table__button', 'table__button_delete');
		removeButton.id = String(this.id) + 'remove';
		removeButton.innerText = 'Удалить';
		removeButton.addEventListener('click', this.handleRemoveButtonClick.bind(this));
		removeButton.append(spinnerRemove);

		const editButton = document.createElement('button')
		editButton.classList.add('table__button', 'table__button_edit')
		editButton.innerText = 'Редактировать';
		editButton.id = String(this.id) + 'edit';
		editButton.addEventListener('click', this.handleEditButtonClick.bind(this));
		editButton.append(spinnerEdit);

		const restoreButton = document.createElement('button');
		restoreButton.classList.add('table__button', 'table__button_restore');
		restoreButton.id = String(this.id) + 'restore';
		restoreButton.innerText = 'Восстановить';
		restoreButton.append(spinnerRestore);
		restoreButton.addEventListener('click', this.handleRestoreButtonClick.bind(this));

		const imagesFormContainer = document.createElement('td');
		imagesFormContainer.classList.add('form__images');

		const formImage = document.createElement('form');
		formImage.classList.add('form__image');
		formImage.method = 'POST'
		formImage.enctype = "multipart/form-data";
		formImage.addEventListener('submit', function(event) {
			event.preventDefault();
			this.handleAddImageButtonClick();
		}.bind(this));

		const nowPathImage = document.createElement('span');
		nowPathImage.classList.add('form__now-image');
		nowPathImage.id = this.id + 'path';
		nowPathImage.innerText = 'Текущее изображение: ' + this.imagePath;

		const imageInputContainer = document.createElement('label');
		imageInputContainer.classList.add('form__label-image');

		const inputImage = document.createElement('input');
		inputImage.classList.add('form__input-image');
		inputImage.id = this.id + 'image';
		inputImage.type = 'file';
		inputImage.name = 'fileToUpload';

		const buttonImage = document.createElement('button')
		buttonImage.classList.add('form__button-image');
		buttonImage.innerText = 'Изменить';
		buttonImage.type = 'submit';

		imageInputContainer.append(inputImage, buttonImage);
		formImage.append(nowPathImage, imageInputContainer);
		imagesFormContainer.append(formImage);

		const actionsColumn = document.createElement('td');
		actionsColumn.classList.add('table__th', 'table__th_button');
		actionsColumn.append(editButton, removeButton, restoreButton);

		trProduct.append(idColumn, titleColumn, descColumn, priceColumn, addedAtColumn, editedAtColumn, isActiveColumn, priorityColumn, tagsColumn, imagesFormContainer, actionsColumn);
		return trProduct;
	}

	handleRemoveButtonClick()
	{
		if (this.removeButtonHandler)
		{
			this.removeButtonHandler(this);
		}
	}

	handleEditButtonClick()
	{
		if (this.editButtonHandler)
		{
			this.editButtonHandler(this);
		}
	}

	handleRestoreButtonClick()
	{
		if (this.restoreButtonHandler)
		{
			this.restoreButtonHandler(this);
		}
	}

	handleAddImageButtonClick()
	{
		if (this.addImageButtonHandler)
		{
			this.addImageButtonHandler(this);
		}
	}

	renderDate(timestamp = null)
	{
		let date;

		if (timestamp === null)
		{
			date = new Date();
		}
		else
		{
			date = new Date(timestamp * 1000);
		}

		const year = date.getFullYear();
		const month = ("0" + (date.getMonth() + 1)).slice(-2);
		const day = date.getDate();
		const hours =  ("0" + date.getHours()).slice(-2);
		const minutes = ("0" + date.getMinutes()).slice(-2);
		const seconds = ("0" + date.getSeconds()).slice(-2);

		return year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
	}

	createTagColumn()
	{
		const tagsColumn = document.createElement('td');
		tagsColumn.classList.add('dropdown_list');

		const dropdownButton = document.createElement('dropdown_button');
		dropdownButton.innerText = 'Показать теги';
		dropdownButton.id = String(this.id) + 'dropdown';
		dropdownButton.addEventListener('click', this.showDropDownTags.bind(this));

		const tagRowContainer = document.createElement('div');
		tagRowContainer.id = String(this.id) + 'productsRowContainer';
		tagRowContainer.classList.add('productsRowContainer');

		const labelsContainer = document.createElement('ul');
		labelsContainer.id = 'productLabel';
		labelsContainer.classList.add('productLabel');
		const idTagLabel = document.createElement('label');
		idTagLabel.innerText = 'Id тега';
		const titleTagLabel = document.createElement('label');
		titleTagLabel.innerText = 'Название тега';

		labelsContainer.append(idTagLabel, titleTagLabel);

		tagRowContainer.append(labelsContainer);
		this.tags.forEach((tag) => {
			const tagRow = document.createElement('ul');
			tagRow.id = 'productRow';
			tagRow.classList.add('productRow');

			const tagIdColumn = document.createElement('li')
			tagIdColumn.classList.add();
			tagIdColumn.innerText = tag['tagId'];

			const tagTitleColumn = document.createElement('li')
			tagTitleColumn.classList.add();
			tagTitleColumn.innerText = tag['tagTitle'];

			tagRow.append(tagIdColumn, tagTitleColumn);
			tagRowContainer.append(tagRow);
		});
		tagsColumn.append(dropdownButton, tagRowContainer);
		return tagsColumn;
	}

	showDropDownTags()
	{
		let tags = document.getElementById(String(this.id) + 'productsRowContainer');

		if (tags.style.display === "block") {
			tags.style.display = "none";
		} else {
			tags.style.display = "block";
		}
	}
}
