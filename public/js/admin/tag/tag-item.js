export class TagItem
{
	id;
	title;
	editButtonHandler;
	removeButtonHandler;

	constructor({ id, title, editButtonHandler, removeButtonHandler })
	{
		this.id = Number(id);
		this.title = String(title);

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
		trProduct.id = String(this.id) + 'tr';
		trProduct.classList.add('table__tr');

		const idColumn = document.createElement('td');
		idColumn.classList.add('table__th', 'table__th_id');
		idColumn.innerText = this.id;


		const titleColumn = document.createElement('td');
		titleColumn.classList.add('table__th');
		titleColumn.innerText = this.title;


		const spinnerRemove = document.createElement('div');
		spinnerRemove.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingRemove = document.createElement('span');
		spinnerLoadingRemove.innerText = 'Loading...';
		spinnerLoadingRemove.classList.add('visually-hidden');
		spinnerRemove.append(spinnerLoadingRemove);

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


		const actionsColumn = document.createElement('td');
		actionsColumn.classList.add('table__th', 'table__th_button');
		actionsColumn.append(editButton, removeButton,);

		trProduct.append(
			idColumn,
			titleColumn,
			actionsColumn);
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
}
