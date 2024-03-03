import { UserItem } from "./user-item.js";
import {OrderItem} from "./user-order-item.js";

export class UserList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	items = [];
	orders = [];
	constructor({ attachToNodeId = '', items, orders})
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
		this.items = this.createItem(items);

		this.orders = orders.map((orderData) => {
			return this.createOrder(orderData)
		})

		this.createItemsContainer();
	}

	createItem(itemData)
	{
		itemData.editButtonHandler = this.handleEditButtonClick.bind(this);
		return new UserItem(itemData);
	}

	createOrder(orderData)
	{
		return new OrderItem(orderData);
	}

	createItemsContainer()
	{
		this.itemsContainer = document.createElement('div')
		this.itemsContainer.classList.add('account__list');

		this.rootNode.append(this.itemsContainer);
	}

	handleEditButtonClick(item)
	{
		if (this.items)
		{
			const shouldRemove = confirm(`Are you sure you want to change this user?`)
			if (!shouldRemove)
			{
				return;
			}

			const name = document.getElementById('name').value;
			const surname = document.getElementById('surname').value;
			const phone = document.getElementById('phone').value;
			const email = document.getElementById('email').value;
			const password = document.getElementById('password').value;

			const newDataInput = {
				id: Number(item.id),
				name: name,
				surname: surname,
				phoneNumber: phone,
				email: email,
				password: password
			}

			const buttonSave = document.getElementById('save');
			buttonSave.disabled = true;

			fetch(
				'/account/edit/',
				{
					method: 'PATCH',
					headers: {
						'Content-Type': 'application/json;charset=utf-8'
					},
					body: JSON.stringify(newDataInput),
				}
			)
				.then((response) => {
					return response.json();
				})
				.then((response) => {
					if (response.result === true)
					{
						const inputPassword = document.getElementById('password');
						inputPassword.value = '';
						buttonSave.disabled = false;
					}
					else
					{
						console.error('Error while deleting item.', response);
						buttonSave.disabled = false;
					}
				})
				.catch((error) => {
					console.error('Error while deleting item.', error);
					buttonSave.disabled = false;
				})
		}
	}
	render()
	{
		const containerOrder = document.createElement('ul');
		containerOrder.classList.add('account__container-order');

		const orderHeading = document.createElement('h2');
		orderHeading.classList.add('account__order-heading');
		orderHeading.innerText = 'Ваши заказы:';

		const userIcon = document.createElement('img');
		userIcon.classList.add('account__image');
		userIcon.src = '/../compressImages/accountIcon.png';
		userIcon.alt = 'accountImage';

		const form = document.createElement('div');
		form.classList.add('account__form');

		this.itemsContainer.append(userIcon, form, orderHeading);

		this.orders.forEach((order) => {
			containerOrder.append(order.render());
		});

		this.rootNode.append(containerOrder);

		form.append(this.items.render());
	}
}
