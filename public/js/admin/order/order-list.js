import { OrderItem } from "./order-item.js";

export class OrderList
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
		return new OrderItem(itemData);
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
		const id = document.getElementById('orderId');
		const deliveryAddress = document.getElementById('deliveryAddress');
		const name = document.getElementById('name');
		const surname = document.getElementById('surname');
		/*const products = document.getElementById('product-dropdown');*/

		/*const productsDropDown = item.createProductsColumn();*/

		id.innerText = item['id'];
		deliveryAddress.value = item['deliveryAddress'];
		name.value = item['name'];
		surname.value = item['surname'];
		/*products.append(productsDropDown);*/
		formEdit.style.display = 'block';
	}

	handleEditCloseButtonClick()
	{
		const formEdit = document.querySelector('.form__box');
		formEdit.style.display = 'none';
	}

	handleAcceptEditButtonClick()
	{
		const shouldRemove = confirm(`Are you sure you want to change this product: ?`)
		if (!shouldRemove)
		{
			return;
		}

		const id = document.getElementById('productId').innerText;
		const title = document.getElementById('title').value;
		const desc = document.getElementById('desc').value;
		const price = document.getElementById('price').value;
		const tags = document.getElementById('tags').value;


		const changeParams = {
			id: Number(id),
			title: title,
			description: desc,
			price: price,
			tags: tags,
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
				if (response.result === true)
				{
					this.items.forEach(item => {
						if (item.id === changeParams.id)
						{
							item.title = changeParams.title;
							item.description = changeParams.description;
							item.price = changeParams.price;
							item.tags = changeParams.tags;
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
				'/admin/order/',
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
						console.error('Error while deleting item.');
						buttonRemove.disabled = false;
					}
				})
				.catch((error) => {
					console.error('Error while deleting item.');
					buttonRemove.disabled = false;
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
			if (column === 'user_id')
				return;
			const tableColumn = document.createElement('th');
			tableColumn.classList.add('table__th', 'table__th-heading');
			tableColumn.innerText = column;

			containerColumn.append(tableColumn);
		})

		const productsColumn = document.createElement('th');
		productsColumn.classList.add('table__th', 'table__th-heading');
		productsColumn.innerText = 'Продукты';
		containerColumn.append(productsColumn);

		const columnAction = document.createElement('th');
		columnAction.classList.add('table__th', 'table__th-heading');
		columnAction.innerText = 'действие';

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
		spanId.id = 'orderId';
		spanId.style.display = 'none';

		/*const productsLabel = document.createElement('label');
		productsLabel.classList.add('form__label');
		productsLabel.htmlFor = 'products';
		productsLabel.innerText = 'Продукты';*/



		/*const dropDown = document.createElement('div');
		const dropDownButton = document.createElement('button');
		dropDownButton.classList.add('dropbtn');
		dropDownButton.innerText = 'Показать';
		dropDownButton.addEventListener('click', this.handleDropdownClick.bind(dropDownButton))*/

		/*const dropDownContent
		dropDownContent.id = 'product-dropdown';
		dropDownContent.classList.add('dropdown-content');*/



		const deliveryAddressLabel = document.createElement('label');
		deliveryAddressLabel.classList.add('form__label');
		deliveryAddressLabel.htmlFor = 'deliveryAddress';
		deliveryAddressLabel.innerText = 'Адрес доставки';

		const deliveryAddressInput = document.createElement('input');
		deliveryAddressInput.classList.add('form__input');
		deliveryAddressInput.id = 'deliveryAddress';
		deliveryAddressInput.type = 'text';
		deliveryAddressInput.name = 'deliveryAddress';

		const nameLabel = document.createElement('label');
		nameLabel.classList.add('form__label');
		nameLabel.htmlFor = 'name';
		nameLabel.innerText = 'Имя';

		const nameInput = document.createElement('input');
		nameInput.classList.add('form__input');
		nameInput.id = 'name';
		nameInput.type = 'text';
		nameInput.name = 'name';

		const surnameLabel = document.createElement('label');
		surnameLabel.classList.add('form__label');
		surnameLabel.htmlFor = 'name';
		surnameLabel.innerText = 'Фамилия';

		const surnameInput = document.createElement('input');
		surnameInput.classList.add('form__input');
		surnameInput.id = 'surname';
		surnameInput.type = 'text';
		surnameInput.name = 'surname';

		const acceptButton = document.createElement('button');
		acceptButton.classList.add('form__button','form__button_change');
		acceptButton.id = 'changed';
		acceptButton.type = 'submit';
		acceptButton.name = 'changeProduct';
		acceptButton.innerText = 'Редактировать';
		acceptButton.addEventListener('click', this.handleAcceptEditButtonClick.bind(this))

		form.append(spanId, deliveryAddressLabel, deliveryAddressInput,
			nameLabel, nameInput,
			surnameLabel, surnameInput,/* productsLabel, dropDownContent*/ acceptButton);
		formContainer.append(closeButton, form);
		formBox.append(formContainer);

		return formBox;
	}

	handleDropdownClick(button)
	{
		button.classList.toggle("show");
	}

}
