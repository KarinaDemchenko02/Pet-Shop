export class ProductItem
{
	id;
	title;
	description;
	price;
	addedAt;
	editedAt;
	isActive;
	editButtonHandler;
	removeButtonHandler;

	constructor({ id, title, description, price, addedAt, editedAt, isActive, editButtonHandler, removeButtonHandler })
	{
		this.id = Number(id);
		this.title = String(title);
		this.description = String(description);
		this.price = Number(price);
		this.addedAt = String(addedAt);
		this.editedAt = String(editedAt);
		this.isActive = Boolean(isActive);

		if (typeof editButtonHandler === 'function')
		{
			this.editButtonHandler = editButtonHandler;
		}

		if (typeof removeButtonHandler === 'function')
		{
			this.removeButtonHandler = removeButtonHandler;
		}
	}

	render()
	{
		const trProduct = document.createElement('tr');
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

		const removeButton = document.createElement('button');
		removeButton.classList.add('table__button', 'table__button_delete');
		removeButton.innerText = 'Удалить';
		removeButton.addEventListener('click', this.handleRemoveButtonClick.bind(this));

		const editButton = document.createElement('button')
		editButton.classList.add('table__button', 'table__button_edit')
		editButton.innerText = 'Редактировать';
		editButton.addEventListener('click', this.handleEditButtonClick.bind(this));

		const actionsColumn = document.createElement('td');
		actionsColumn.classList.add('table__th', 'table__th_button');
		actionsColumn.append(editButton, removeButton);

		trProduct.append(idColumn, titleColumn, descColumn, priceColumn, addedAtColumn, editedAtColumn, isActiveColumn, actionsColumn);
		return trProduct;
		/*
		const removeButton = document.createElement('button');
		removeButton.classList.add('btn', 'btn-danger');
		removeButton.innerText = 'remove';
		removeButton.addEventListener('click', this.handleRemoveButtonClick.bind(this))

		const actionsColumn = document.createElement('div');
		actionsColumn.classList.add('col-3');
		actionsColumn.append(removeButton);

		row.append(titleColumn, descriptionColumn, priceColumn, actionsColumn)

		return row;*/
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
}
