import {UserItem} from "./user-item.js";

export class UserList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	items = [];
	columns= [];
	orders = [];
	constructor({ attachToNodeId = '', items, columns, orders })
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

		this.orders = orders;

		this.createItemsContainer()
	}

	createItem(itemData)
	{
		itemData.editButtonHandler = this.handleEditButtonClick.bind(this);
		return new UserItem(itemData);
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
		const role = document.getElementById('price');

		const buttonAdd = document.getElementById('add');
		const buttonEdit = document.getElementById('changed');

		id.innerText = item['id'];
		status.value = item['title'];
		desc.value = item['description'];
		price.value = item['price'];

		id.style.display = 'none';
		formEdit.style.display = 'block';
		buttonAdd.style.display = 'none';
		buttonEdit.style.display = 'block';

		this.createSelectTags(item['tags']);
	}

	/*handleAddButtonClick()
	{
		const id = document.getElementById('productId');
		const title = document.getElementById('title');
		const desc = document.getElementById('desc');
		const price = document.getElementById('price');

		const buttonAdd = document.getElementById('add');
		const buttonEdit = document.getElementById('changed');

		id.innerText = '';
		title.value = '';
		desc.value = '';
		price.value = '';

		const formEdit = document.querySelector('.form__box');
		formEdit.style.display = 'block';

		buttonAdd.style.display = 'block';
		buttonEdit.style.display = 'none';

		const tagsContainer = document.querySelector('.form__container-tag');
		tagsContainer.innerHTML = '';

		this.handleAddTags();
	}*/

	handleEditCloseButtonClick()
	{
		const formEdit = document.querySelector('.form__box');
		formEdit.style.display = 'none';
	}

	handleDeleteRole(event)
	{
		const container = event.target.parentNode.parentNode;
		container.parentNode.removeChild(container);
	}

	handleAddRole()
	{
		const tagsContainer = document.querySelector('.form__container-tag');

		let tagsSelect;

		const selectContainer = document.createElement('div');
		selectContainer.classList.add('form__select-container');

		const deleteTag = document.createElement('button');
		deleteTag.classList.add('form__delete-tag');
		deleteTag.addEventListener('click', this.handleDeleteRole.bind(this));

		const iconDelete = document.createElement('i');
		iconDelete.classList.add('material-icons', 'form__icon-delete');
		iconDelete.innerText = 'close';

		tagsSelect = document.createElement('select');
		tagsSelect.classList.add('form__select-input-tag', 'form-select')
		tagsSelect.name = 'tags';

		this.tags.forEach(tagAll => {
			const option = document.createElement('option');
			option.classList.add('form__option');
			option.value = tagAll['id'];
			option.innerText = tagAll['title'];

			tagsSelect.append(option);
		})

		selectContainer.append(tagsSelect);
		deleteTag.append(iconDelete);
		selectContainer.append(deleteTag);
		tagsContainer.append(selectContainer);
	}

	handleAcceptEditButtonClick()
	{
		const shouldRemove = confirm(`Are you sure you want to delete this product: ?`)
		if (!shouldRemove)
		{
			return;
		}

		const id = document.getElementById('productId').innerText;
		const title = document.getElementById('title').value;
		const desc = document.getElementById('desc').value;
		const price = document.getElementById('price').value;
		const tags = document.querySelectorAll('.form__select-input-tag');

		let idTags = [];
		let objectTags = [];

		tags.forEach(tag => {
			const idTag = tag.options[tag.selectedIndex].value;
			const textTag = tag.options[tag.selectedIndex].text;

			objectTags.push({
				tagId: idTag,
				tagTitle: textTag
			});

			idTags.push(Number(idTag));
		})

		objectTags = Array.from(new Set(objectTags.map(item => JSON.stringify(item)))).map(item => JSON.parse(item));

		const changeParams = {
			id: Number(id),
			title: title,
			description: desc,
			price: price,
			tags: idTags,
		}

		const buttonEdit = document.getElementById(changeParams.id + 'edit');
		buttonEdit.disabled = true;

		fetch(
			'/admin/product/change/',
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
				if (response.result)
				{
					this.items.forEach(item => {
						if (item.id === changeParams.id)
						{
							item.title = changeParams.title;
							item.description = changeParams.description;
							item.price = changeParams.price;
							item.tags = objectTags;
							item.editedAt = item.renderDate();

							return true;
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

	handleAcceptAddButtonClick()
	{
		const shouldRemove = confirm(`Are you sure you want to delete this product: ?`)
		if (!shouldRemove)
		{
			return;
		}

		const title = document.getElementById('title').value;
		const desc = document.getElementById('desc').value;
		const price = document.getElementById('price').value;

		const tags = document.querySelectorAll('.form__select-input-tag');

		let idTags = [];
		let objectTags = [];

		tags.forEach(tag => {
			const idTag = tag.options[tag.selectedIndex].value;
			const textTag = tag.options[tag.selectedIndex].text;

			objectTags.push({
				tagId: idTag,
				tagTitle: textTag
			});

			idTags.push(Number(idTag));
		})

		objectTags = Array.from(new Set(objectTags.map(item => JSON.stringify(item)))).map(item => JSON.parse(item));

		const addParams = {
			title: title,
			description: desc,
			price: price,
			tags: idTags,
		}

		const buttonAdd = document.getElementById('add');
		buttonAdd.disabled = true;

		fetch(
			'/admin/product/add/',
			{
				method: 'POST',
				headers: {
					'Content-Type': 'application/json;charset=utf-8'
				},
				body: JSON.stringify(addParams),
			}
		)
			.then((response) => {
				return response.json();
			})
			.then((response) => {
				if (response.id)
				{
					const table = document.querySelector('.table');
					let isExecuted = false;

					this.items.forEach(item => {
						if (!isExecuted) {
							item.id = response.id
							item.title = addParams.title;
							item.description = addParams.description;
							item.price = addParams.price;
							item.isActive = true;
							item.tags = objectTags;
							item.addedAt =  item.renderDate();
							item.editedAt = item.renderDate();
							table.append(item.render());

							isExecuted = true;
						}
					});

					const formEdit = document.querySelector('.form__box');
					formEdit.style.display = 'none';

					buttonAdd.disabled = false;
				}
				else
				{
					console.error(response.errors);
					buttonAdd.disabled = false;
				}
			})
			.catch((error) => {
				console.error('Error while changing item.');
				buttonAdd.disabled = false;
			})
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

		const columnImages = document.createElement('th');
		columnImages.classList.add('table__th', 'table__th-heading');
		columnImages.innerText = 'изображение';

		const columnAction = document.createElement('th');
		columnAction.classList.add('table__th', 'table__th-heading');
		columnAction.innerText = 'действие';

		const addButton = document.createElement('button');
		addButton.classList.add('form__button', 'form__button_add');
		addButton.id = 'addOpen';
		addButton.innerText = 'Добавить';
		addButton.addEventListener('click', this.handleAddButtonClick.bind(this));

		containerColumn.append(columnImages, columnAction);
		table.append(containerColumn);

		this.itemsContainer.append(addButton);

		this.itemsContainer.append(table, this.renderForm());

		this.items.forEach((item) => {
			table.append(item.render());
		});
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
		spanId.id = 'productId'

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

		const tagsLabel = document.createElement('label');
		tagsLabel.classList.add('form__label');
		tagsLabel.htmlFor = 'tags';
		tagsLabel.innerText = 'Теги';

		const containerTagsSelect = document.createElement('div');
		containerTagsSelect.classList.add('form__container-tag');

		const acceptButton = document.createElement('button');
		acceptButton.classList.add('form__button', 'form__button_change');
		acceptButton.id = 'changed';
		acceptButton.type = 'submit';
		acceptButton.name = 'changeProduct';
		acceptButton.innerText = 'Редактировать';
		acceptButton.addEventListener('click', this.handleAcceptEditButtonClick.bind(this));

		const spinnerAdd = document.createElement('div');
		spinnerAdd.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingAdd = document.createElement('span');
		spinnerLoadingAdd.innerText = 'Loading...';
		spinnerLoadingAdd.classList.add('visually-hidden');
		spinnerAdd.append(spinnerLoadingAdd);

		const acceptAddButton = document.createElement('button');
		acceptAddButton.classList.add('form__button', 'form__button_add');
		acceptAddButton.id = 'add';
		acceptAddButton.type = 'submit';
		acceptAddButton.name = 'addProduct';
		acceptAddButton.innerText = 'Добавить';
		acceptAddButton.addEventListener('click', this.handleAcceptAddButtonClick.bind(this));

		const addTags = document.createElement('button');
		addTags.classList.add('form__add-tag');
		addTags.innerText = 'Добавить тег';
		addTags.addEventListener('click', this.handleAddRole.bind(this));

		const icon = document.createElement('i');
		icon.classList.add('material-icons', 'form__icon-add');
		icon.innerText = 'add';

		addTags.append(icon);

		acceptAddButton.append(spinnerAdd);

		form.append(spanId, titleLabel, titleInput, descLabel, descInput,
			priceLabel, priceInput, tagsLabel, containerTagsSelect, addTags, acceptButton, acceptAddButton);
		formContainer.append(closeButton, form);
		formBox.append(formContainer);

		return formBox;
	}

	createSelectTags(tags)
	{
		const tagsContainer = document.querySelector('.form__container-tag');

		tagsContainer.innerHTML = '';

		let tagsSelect;

		tags.forEach(tag => {
			const selectContainer = document.createElement('div');
			selectContainer.classList.add('form__select-container');

			const deleteTag = document.createElement('button');
			deleteTag.classList.add('form__delete-tag');
			deleteTag.addEventListener('click', this.handleDeleteRole.bind(this));

			const iconDelete = document.createElement('i');
			iconDelete.classList.add('material-icons', 'form__icon-delete');
			iconDelete.innerText = 'close';

			tagsSelect = document.createElement('select');
			tagsSelect.classList.add('form__select-input-tag', 'form-select')
			tagsSelect.name = 'tags';

			this.tags.forEach(tagAll => {
				const option = document.createElement('option');
				option.classList.add('form__option');
				option.value = tagAll['id'];
				option.innerText = tagAll['title'];

				if (option.value === tag['tagId'])
				{
					option.selected = true;
				}

				tagsSelect.append(option);
			})

			selectContainer.append(tagsSelect);
			deleteTag.append(iconDelete);
			selectContainer.append(deleteTag);
			tagsContainer.append(selectContainer);
		})
	}
}
