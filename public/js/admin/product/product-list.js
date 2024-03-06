import {ProductItem} from "./product-item.js";
import {Error} from "../../main/error/error.js";

export class ProductList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	items = [];
	columns= [];
	tags = [];
	currentPagination = new URLSearchParams(window.location.search).get('page');

	constructor({ attachToNodeId = '', items, columns, tags })
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

		this.tags = tags;

		this.createItemsContainer()
	}

	createItem(itemData)
	{
		itemData.removeButtonHandler = this.handleRemoveButtonClick.bind(this);
		itemData.editButtonHandler = this.handleEditButtonClick.bind(this);
		itemData.restoreButtonHandler = this.handleRestoreButtonClick.bind(this);
		itemData.addImageButtonHandler = this.handleAddImageButtonClick.bind(this);
		return new ProductItem(itemData);
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
		const desc = document.getElementById('desc');
		const price = document.getElementById('price');
		const priority = document.getElementById('priority');

		const buttonAdd = document.getElementById('add');
		const buttonEdit = document.getElementById('changed');

		id.innerText = item['id'];
		title.value = item['title'];
		desc.value = item['description'];
		price.value = item['price'];
		priority.value = item['priority'];


		id.style.display = 'none';
		formEdit.style.display = 'block';
		buttonAdd.style.display = 'none';
		buttonEdit.style.display = 'block';

		this.createSelectTags(item['tags']);
	}

	handleAddButtonClick()
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
	}

	handleEditCloseButtonClick()
	{
		const formEdit = document.querySelector('.form__box');
		formEdit.style.display = 'none';
	}

	handleDeleteTag(event)
	{
		const container = event.target.parentNode;

		container.parentNode.removeChild(container);
	}

	handleAddTags()
	{
		const tagsContainer = document.querySelector('.form__container-tag');

		let tagsSelect;

		const selectContainer = document.createElement('div');
		selectContainer.classList.add('form__select-container');

		const deleteTag = document.createElement('button');
		deleteTag.classList.add('form__delete-tag');
		deleteTag.addEventListener('click', this.handleDeleteTag.bind(this));

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
		const priority = document.getElementById('priority').value;
		const tags = document.querySelectorAll('.form__select-input-tag');

		const buttonEditSend = document.getElementById('changed');

		const formContainer = document.querySelector('.form');
		const errorContainer = document.querySelector('.form__alert-container');

		if (errorContainer) {
			errorContainer.remove();
		}

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
			priority: priority,
			tags: idTags,
		}

		const buttonEdit = document.getElementById(changeParams.id + 'edit');
		buttonEdit.disabled = true;
		buttonEditSend.disabled = true;

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
			.then(async (response) => {
				if (response.result) {
					this.items.forEach(item => {
						if (item.id === changeParams.id) {
							item.title = changeParams.title;
							item.description = changeParams.description;
							item.price = changeParams.price;
							item.priority = changeParams.priority;
							item.tags = objectTags;
							item.editedAt = item.renderDate();

							return true;
						}
					})

					buttonEdit.disabled = false;
					buttonEditSend.disabled = false;

					await this.render();

				} else {
					console.error(response.errors);
					new Error(`Что-то пошло не так`,
						formContainer).printError();
					buttonEdit.disabled = false;
					buttonEditSend.disabled = false;
				}
			})
			.catch((error) => {
				console.error('Error while changing item.');
				new Error('Что-то пошло не так. Проверьте введенные Вами данные или повторить попытку позже',
					formContainer).printError();
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

		const formContainer = document.querySelector('.form');
		const errorContainer = document.querySelector('.form__alert-container');

		if (errorContainer) {
			errorContainer.remove();
		}

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
					new Error('Товар с таким ID не найден', formContainer).printError();
					buttonAdd.disabled = false;
				}
			})
			.catch((error) => {
				console.error('Error while changing item.');
				new Error('Что-то пошло не так. Проверьте введенные Вами данные или повторить попытку позже',
					formContainer).printError();
				buttonAdd.disabled = false;
			})
	}
	handleRemoveButtonClick(item)
	{
		const itemIndex = this.items.indexOf(item);

		if (itemIndex > -1)
		{
			const shouldRemove = confirm(`Are you sure you want to delete this product: ${item.title}?`)
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
				'/admin/product/disable/',
				{
					method: 'PATCH',
					headers: {
						'Content-Type': 'application/json;charset=utf-8'
					},
					body: JSON.stringify(removeParams),
				}
			)
				.then((response) => {
					return response.json();
				})
				.then(async (response) => {
					if (response.result) {
						this.items[itemIndex].isActive = false;
						buttonRemove.disabled = false;
						await this.render();
					}

				})
				.catch((error) => {
					console.error('Error while deleting item.');
					buttonRemove.disabled = false;
				})
		}
	}

	handleRestoreButtonClick(item)
	{
		const itemIndex = this.items.indexOf(item);
		if (itemIndex > -1)
		{
			const shouldRemove = confirm(`Are you sure you want to restore this product: ${item.title}?`)
			if (!shouldRemove)
			{
				return;
			}

			const removeParams = {
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
					body: JSON.stringify(removeParams),
				}
			)
				.then((response) => {
					return response.json();
				})
				.then(async (response) => {
					if (response.result) {
						this.items[itemIndex].isActive = true;
						buttonRestore.disabled = false;
						await this.render();
					} else {
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

	handleAddImageButtonClick(item)
	{
		const itemIndex = this.items.indexOf(item);
		if (itemIndex > -1)
		{
			const shouldRemove = confirm(`Are you sure you want to restore this product: ${item.title}?`)
			if (!shouldRemove)
			{
				return;
			}

			const inputFile = document.getElementById(item.id + 'image').files[0];

			const formData = new FormData();
			formData.append('idProduct', item.id);
			formData.append('imagePath', inputFile);
			fetch(
				'/admin/product/image/',
				{
					method: 'POST',
					body: formData,
				}
			)
				.then((response) => {
					return response.json();
				})
				.then((response) => {
					console.log(response);
					if (response.result)
					{
						const nowPath = document.getElementById(item.id + 'path');
						nowPath.innerText = inputFile.name;
					}
					else
					{
						console.error('Error while deleting item.');
					}
				})
				.catch((error) => {
					console.error('Error while deleting item.');
				})
		}
	}

	handleChangePaginationButtonClick(event) {
		const page = event.target.innerText;

		let currentUrl = window.location.href;

		this.currentPagination = page;

		let newUrl = new URL(currentUrl);
		newUrl.searchParams.set('page', page);

		window.history.replaceState({}, '', newUrl);

		fetch(
			`/productsAdmin-json/?page=${page}`,
			{
				method: 'GET',
			}
		)
			.then((response) => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then(async (response) => {
				if (response.nextPage.length !== 0) {
					this.currentPagination = Number(page) + 1;
				}

				response.products.forEach(product => {

					let arr = [];

					for (const key in product.tags) {
						const info = product.tags[key];
						arr.push(info)
					}

					product.tags = arr.map((tag) => {
						return {
							tagId: tag.id,
							tagTitle: tag.title,
						};
					});
				});

				this.items = response.products.map((itemData) => {
					return this.createItem(itemData)
				})

				await this.render();
			})
			.catch((error) => {
				console.error('Error while changing item:', error);
			});
	}

	async render() {
		if (this.items.length === 0) {
			this.rootNode.innerHTML = '';
			const modal = new Error('Данная страница не найдена!', null, '/admin/').render();
			this.rootNode.append(modal);

			return false;
		}
		this.itemsContainer.innerHTML = '';

		const paginationContainer = document.createElement('div');
		paginationContainer.classList.add('table__pagination-container')

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

		this.itemsContainer.append(addButton, table, this.renderForm());

		this.items.forEach((item) => {
			table.append(item.render());
		});

		await this.renderPagination();
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

		const priorityLabel = document.createElement('label');
		priorityLabel.classList.add('form__label');
		priorityLabel.htmlFor = 'priority';
		priorityLabel.innerText = 'Приоритет';

		const priorityInput = document.createElement('input');
		priorityInput.classList.add('form__input');
		priorityInput.id = 'priority';
		priorityInput.type = 'text';
		priorityInput.name = 'priority';

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
		addTags.addEventListener('click', this.handleAddTags.bind(this));

		const icon = document.createElement('i');
		icon.classList.add('material-icons', 'form__icon-add');
		icon.innerText = 'add';

		addTags.append(icon);

		acceptAddButton.append(spinnerAdd);

		form.append(spanId, titleLabel, titleInput, descLabel, descInput,
			priceLabel, priceInput, priorityLabel, priorityInput,
			tagsLabel, containerTagsSelect, addTags, acceptButton, acceptAddButton);
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
			deleteTag.addEventListener('click', this.handleDeleteTag.bind(this));

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
			selectContainer.append(deleteTag);
			tagsContainer.append(selectContainer);
		})
	}

	async renderPagination() {
		const paginationContainer = document.createElement('div');
		paginationContainer.id = 'buttonPagination'
		paginationContainer.classList.add('pagination');

		let current = 0;

		if (!this.currentPagination) {
			const result = await this.checkPageNumberOne();

			if (result) {
				current = 1;
			}

			this.currentPagination = 1;
		}


		if (this.currentPagination === '1') {
			const result = await this.checkPageNumberOne();

			if (result) {
				current = 1;
			}
		}

		let currentPage = parseInt(new URLSearchParams(window.location.search).get('page') || '1');
		const startIndex = Math.max(1, currentPage - 1);
		const endIndex = Math.min(parseInt(this.currentPagination), currentPage + 1);

		for (let i = startIndex; i <= endIndex + current; i++) {
			const buttonPagination = document.createElement('button');
			buttonPagination.classList.add('pagination__button');
			buttonPagination.innerText = String(i);
			buttonPagination.addEventListener('click', this.handleChangePaginationButtonClick.bind(this));

			if (currentPage === null) {
				currentPage = '1';
			}
			if (buttonPagination.innerText === String(currentPage)) {
				buttonPagination.classList.add('is-active');
			}

			paginationContainer.append(buttonPagination)

			this.itemsContainer.append(paginationContainer);
		}
	}

	checkPageNumberOne()
	{
		return fetch(
			`/productsAdmin-json/?page=1`,
			{
				method: 'GET',
			}
		)
			.then((response) => {
				if (!response.ok) {
					throw new Error('Network response was not ok');
				}
				return response.json();
			})
			.then((response) => {
				return response.nextPage.length !== 0;
			})
			.catch((error) => {
				console.error('Error while checking for items on next page:', error);
			});
	}
}
