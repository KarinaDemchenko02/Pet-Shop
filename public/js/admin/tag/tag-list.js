import { TagItem } from "../tag/tag-item.js";

export class TagList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	items = [];
	columns= [];
	constructor({ attachToNodeId = '', items, columns })
	{
		if (attachToNodeId === '')
		{
			throw new Error('attachToNodeId must be a filled string.');
		}

		const rootNode = document.getElementById(attachToNodeId)
		if (!rootNode)
		{
			throw new Error(`There is no element with this ID: ${attachToNodeId}.`);
		}

		this.rootNode = rootNode;
		this.items = items.map((itemData) => {
			return this.createItem(itemData)
		})

		this.columns = columns;

		this.createItemsContainer()
	}


	createItem(itemData)
	{
		itemData.removeButtonHandler = this.handleRemoveButtonClick.bind(this);
		itemData.editButtonHandler = this.handleEditButtonClick.bind(this);
		itemData.restoreButtonHandler = this.handleRestoreButtonClick.bind(this);
		return new TagItem(itemData);
	}

	createItemsContainer()
	{
		this.itemsContainer = document.createElement('div')
		this.itemsContainer.classList.add('product-list');

		this.rootNode.append(this.itemsContainer);
	}

	handleEditButtonClick(item)
	{
		const formEdit = document.querySelector('.form__box');
		const id = document.getElementById('productId');
		const title = document.getElementById('title');

		id.innerText = item['id'];
		title.value = item['title'];

		formEdit.style.display = 'block';
	}

	handleEditCloseButtonClick()
	{
		const formEdit = document.querySelector('.form__box');
		formEdit.style.display = 'none';
	}

	handleAcceptEditButtonClick()
	{
		const shouldChange = confirm(`Are you sure you want to change this tag: ?`)
		if (!shouldChange)
		{
			return;
		}

		const id = document.getElementById('productId').innerText;
		const title = document.getElementById('title').value;


		const changeParams = {
			id: Number(id),
			title: title,
		}

		const buttonEdit = document.getElementById(changeParams.id + 'edit');
		buttonEdit.disabled = true;

		fetch(
			'/admin/tag/change/',
			{
				method: 'PATCH',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
				body: JSON.stringify(changeParams),
			}
		)
			.then((response) => {
				return response.json();
			})
			.then((response) => {
				if (response.result === true)
				{
					this.items.forEach(item => {
						if (item.id === changeParams.id)
						{
							item.title = changeParams.title;
						}
					})

					buttonEdit.disabled = false;

					this.render();
				}
				else
				{
					console.error(response.errors);
					buttonEdit.disabled = false;
				}
			})
			.catch((error) => {
				console.error('Error while changing item.');
				buttonEdit.disabled = false;
			})
	}
	handleRemoveButtonClick(item)
	{
		const itemIndex = this.items.indexOf(item);

		if (itemIndex > -1)
		{
			const shouldRemove = confirm(`Are you sure you want to delete this tag: ${item.title}?`)
			if (!shouldRemove)
			{
				return;
			}

			const removeParams = {
				id: item.id,
			}

			const buttonRemove = document.getElementById(item.id + 'remove');
			buttonRemove.disabled = true;

			fetch(
				'/admin/tag/',
				{
					method: 'DELETE',
					headers: {
						'Content-Type': 'application/json;charset=utf-8'
					},
					body: JSON.stringify(removeParams),
				}
			)
				.then((response) => {
					return response.json();
				})
				.then((response) => {
					if (response.result === true)
					{
						this.items[itemIndex].isActive = false;
						buttonRemove.disabled = false;
						this.render();
					}
					else
					{
						console.error('Error while disabling item.');
						buttonRemove.disabled = false;
					}
				})
				.catch((error) => {
					console.error('Error while disabling item.');
					buttonRemove.disabled = false;
				})
		}
	}

	handleRestoreButtonClick(item)
	{
		const itemIndex = this.items.indexOf(item);
		if (itemIndex > -1)
		{
			const shouldRestore = confirm(`Are you sure you want to restore this product: ${item.title}?`)
			if (!shouldRestore)
			{
				return;
			}

			const restoreParams = {
				id: item.id,
			}

			const buttonRestore = document.getElementById(item.id + 'restore');
			buttonRestore.disabled = true;

			fetch(
				'/admin/product/restore/',
				{
					method: 'PATCH',
					headers: {
						'Content-Type': 'application/json;charset=utf-8'
					},
					body: JSON.stringify(restoreParams),
				}
			)
				.then((response) => {
					return response.json();
				})
				.then((response) => {
					if (response.result === true)
					{
						this.items[itemIndex].isActive = true;
						buttonRestore.disabled = false;
						this.render();
					}
					else
					{
						console.error('Error while deleting item.');
						buttonRestore.disabled = false;
					}
				})
				.catch((error) => {
					console.error('Error while deleting item.');
					buttonRestore.disabled = false;
				})
		}
	}
	render()
	{
		this.itemsContainer.innerHTML = '';

		const table = document.createElement('table');
		table.classList.add('table');

		const containerColumn = document.createElement('tr');
		containerColumn.classList.add('table__tr');

		this.columns.forEach(column => {
			const tableColumn = document.createElement('th');
			tableColumn.classList.add('table__th', 'table__th-heading');
			tableColumn.innerText = column;

			containerColumn.append(tableColumn);
		})

		/*const tagsColumn = document.createElement('th');
		tagsColumn.classList.add('table__th', 'table__th-heading');
		tagsColumn.innerText = 'Теги';
		containerColumn.append(tagsColumn);*/

		const columnAction = document.createElement('th');
		columnAction.classList.add('table__th', 'table__th-heading');
		columnAction.innerText = 'Действие';

		containerColumn.append(columnAction);
		table.append(containerColumn);


		this.itemsContainer.append(table, this.renderForm());

		this.items.forEach((item) => {
			table.append(item.render());
		})
	}

	renderForm()
	{
		const formBox = document.createElement('div');
		formBox.classList.add('form__box');

		const formContainer = document.createElement('div');
		formContainer.classList.add('form__container');

		const closeButton = document.createElement('button');
		closeButton.classList.add('form__close');
		closeButton.addEventListener('click', this.handleEditCloseButtonClick);
		const closeIcon = document.createElement('i');
		closeIcon.classList.add('form__close-icon', 'material-icons');
		closeIcon.innerText = 'close';
		closeButton.append(closeIcon);

		const form = document.createElement('div');
		form.classList.add('form');

		const spanId = document.createElement('span');
		spanId.id = 'productId';
		spanId.style.display = 'none';

		const titleLabel = document.createElement('label');
		titleLabel.classList.add('form__label');
		titleLabel.htmlFor = 'title';
		titleLabel.innerText = 'Название';

		const titleInput = document.createElement('input');
		titleInput.classList.add('form__input');
		titleInput.id = 'title';
		titleInput.type = 'text';
		titleInput.name = 'title';

		const descLabel = document.createElement('label');
		descLabel.classList.add('form__label');
		descLabel.htmlFor = 'desc';
		descLabel.innerText = 'Описание';

		const descInput = document.createElement('input');
		descInput.classList.add('form__input');
		descInput.id = 'desc';
		descInput.type = 'text';
		descInput.name = 'desc';

		const priceLabel = document.createElement('label');
		priceLabel.classList.add('form__label');
		priceLabel.htmlFor = 'price';
		priceLabel.innerText = 'Цена';

		const priceInput = document.createElement('input');
		priceInput.classList.add('form__input');
		priceInput.id = 'price';
		priceInput.type = 'text';
		priceInput.name = 'price';

		/*const tagsLabel = document.createElement('label');
		tagsLabel.classList.add('form__label');
		tagsLabel.htmlFor = 'tags';
		tagsLabel.innerText = 'Теги';

		const tagsInput = document.createElement('input');
		tagsInput.classList.add('form__input');
		tagsInput.id = 'tags';
		tagsInput.type = 'text';
		tagsInput.name = 'tags';*/

		const acceptButton = document.createElement('button');
		acceptButton.classList.add('form__button','form__button_change');
		acceptButton.id = 'changed';
		acceptButton.type = 'submit';
		acceptButton.name = 'changeProduct';
		acceptButton.innerText = 'Редактировать';
		acceptButton.addEventListener('click', this.handleAcceptEditButtonClick.bind(this))

		form.append(spanId, titleLabel, titleInput, descLabel, descInput,
			priceLabel, priceInput, /*tagsLabel, tagsInput,*/ acceptButton);
		formContainer.append(closeButton, form);
		formBox.append(formContainer);

		return formBox;
	}
}
