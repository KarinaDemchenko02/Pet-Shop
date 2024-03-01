export class UserItem
{
	id;
	email;
	role;
	phone;
	orders;
	editButtonHandler;

	constructor({ id, email, role, phone, orders, editButtonHandler })
	{
		this.id = Number(id);
		this.email = String(email);
		this.role = String(role);
		this.phone = String(phone);
		this.orders = orders;

		if (typeof editButtonHandler === 'function')
		{
			this.editButtonHandler = editButtonHandler;
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

		const emailColumn = document.createElement('td');
		emailColumn.classList.add('table__th', 'table__th_title');
		emailColumn.innerText = this.email;

		const roleColumn = document.createElement('td');
		roleColumn.classList.add('table__th', 'table__th_desc');
		roleColumn.innerText = this.role;

		const phoneColumn = document.createElement('td');
		phoneColumn.classList.add('table__th', 'table__th_price');
		phoneColumn.innerText = this.phone;

		const ordersColumn = this.createOrderColumn();
		ordersColumn.classList.add('table__th', 'table__th_tags');

		const spinnerEdit = document.createElement('div');
		spinnerEdit.classList.add('spinner-border', 'text-light', 'spinner-action');
		const spinnerLoadingEdit = document.createElement('span');
		spinnerLoadingEdit.innerText = 'Loading...';
		spinnerLoadingEdit.classList.add('visually-hidden');
		spinnerEdit.append(spinnerLoadingEdit);

		const editButton = document.createElement('button')
		editButton.classList.add('table__button', 'table__button_edit')
		editButton.innerText = 'Редактировать';
		editButton.id = String(this.id) + 'edit';
		editButton.addEventListener('click', this.handleEditButtonClick.bind(this));
		editButton.append(spinnerEdit);

		const actionsColumn = document.createElement('td');
		actionsColumn.classList.add('table__th', 'table__th_button');
		actionsColumn.append(editButton);

		trProduct.append(idColumn, emailColumn, roleColumn, phoneColumn, ordersColumn, actionsColumn);
		return trProduct;
	}

	handleEditButtonClick()
	{
		if (this.editButtonHandler)
		{
			this.editButtonHandler(this);
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

	createOrderColumn()
	{
		const orderColumn = document.createElement('td');
		orderColumn.classList.add('dropdown_list');

		const dropdownButton = document.createElement('dropdown_button');
		dropdownButton.innerText = 'Показать заказы';
		dropdownButton.id = String(this.id) + 'dropdown';
		dropdownButton.addEventListener('click', this.showDropDownTags.bind(this));

		const orderRowContainer = document.createElement('div');
		orderRowContainer.id = String(this.id) + 'productsRowContainer';
		orderRowContainer.classList.add('productsRowContainer');

		const labelsContainer = document.createElement('ul');
		labelsContainer.id = 'productLabel';
		labelsContainer.classList.add('productLabel');
		const idOrderLabel = document.createElement('label');
		idOrderLabel.innerText = 'Id заказа';
		const statusOrderLabel = document.createElement('label');
		statusOrderLabel.innerText = 'Статус заказа';

		labelsContainer.append(idOrderLabel, statusOrderLabel);

		orderRowContainer.append(labelsContainer);
		this.tags.forEach((order) => {
			const orderRow = document.createElement('ul');
			orderRow.id = 'productRow';
			orderRow.classList.add('productRow');

			const orderIdColumn = document.createElement('li')
			orderIdColumn.classList.add();
			orderIdColumn.innerText = order['orderId'];

			const orderStatusColumn = document.createElement('li')
			orderStatusColumn.classList.add();
			orderStatusColumn.innerText = order['orderStatus'];

			orderRow.append(orderIdColumn, orderStatusColumn);
			orderRowContainer.append(orderRow);
		});
		orderColumn.append(dropdownButton, orderRowContainer);
		return orderColumn;
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
