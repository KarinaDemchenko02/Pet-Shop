export class ProductItem
{
	id;
	title;
	description;
	price;
	tags
	addedAt;
	editedAt;
	isActive;
	editButtonHandler;
	removeButtonHandler;
	restoreButtonHandler;

	constructor({ id, title, description, tags, price, addedAt, editedAt, isActive, editButtonHandler, removeButtonHandler, restoreButtonHandler })
	{
		this.id = Number(id);
		this.title = String(title);
		this.description = String(description);
		this.price = Number(price);
		this.addedAt = new Date(Number(addedAt)*1000).toDateString();
		this.editedAt = new Date(Number(editedAt)*1000).toDateString();
		this.isActive = Boolean(isActive);
		this.tags = String(tags);

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

		const tagsColumn = document.createElement('td');
		tagsColumn.classList.add('table__th', 'table__th_tags');
		tagsColumn.innerText = this.tags;

		const addedAtColumn = document.createElement('td');
		addedAtColumn.classList.add('table__th');
		addedAtColumn.innerText = this.addedAt;

		const editedAtColumn = document.createElement('td');
		editedAtColumn.classList.add('table__th');
		editedAtColumn.innerText = this.editedAt;

		const isActiveColumn = document.createElement('td');
		isActiveColumn.classList.add('table__th');
		isActiveColumn.innerText = this.isActive;

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

		const actionsColumn = document.createElement('td');
		actionsColumn.classList.add('table__th', 'table__th_button');
		actionsColumn.append(editButton, removeButton, restoreButton);

		trProduct.append(idColumn, titleColumn, descColumn, priceColumn, addedAtColumn, editedAtColumn, isActiveColumn, tagsColumn, actionsColumn);
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
}
