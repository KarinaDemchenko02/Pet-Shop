import { UserItem } from "./user-item.js";

export class UserList
{
	attachToNodeId = '';
	rootNode;
	itemsContainer;
	items = [];
	constructor({ attachToNodeId = '', items })
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

		this.createItemsContainer()
	}


	createItem(itemData)
	{
		itemData.removeButtonHandler = this.handleRemoveButtonClick.bind(this);
		itemData.editButtonHandler = this.handleEditButtonClick.bind(this);
		itemData.restoreButtonHandler = this.handleRestoreButtonClick.bind(this);
		return new UserItem(itemData);
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
			const shouldRemove = confirm(`Are you sure you want to restore this user: ${item.id}?`)
			if (!shouldRemove)
			{
				return;
			}

			const inputName = document.getElementById('name');
			const inputPhone = document.getElementById('phone');
			const inputEmail = document.getElementById('email');
			const inputPassword = document.getElementById('password')

			const newDataInput = {
				id: item.id,
				name: inputName.value,
				phoneNumber: inputPhone.value,
				email: inputEmail.value,
				password: inputPassword.value,
			}

			const buttonSave = document.getElementById('save');
			buttonSave.disabled = true;

			fetch(
				'/account/edit/',
				{
					method: 'POST',
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
					if (response.result === 'Y')
					{
						const inputPassword = document.getElementById('password');
						inputPassword.value = '';
						buttonSave.disabled = false;
						console.log('success');
					}
					else
					{
						console.error('Error while deleting item.');
						buttonSave.disabled = false;
					}
				})
				.catch((error) => {
					console.error('Error while deleting item.');
					buttonSave.disabled = false;
				})
		}
	}
	handleRemoveButtonClick(item)
	{

	}

	handleRestoreButtonClick(item)
	{

	}
	render()
	{
		const userIcon = document.createElement('img');
		userIcon.classList.add('account__image');
		userIcon.src = '/../compressImages/accountIcon.png';
		userIcon.alt = 'accountImage';

		const form = document.createElement('div');
		form.classList.add('account__form');


		this.itemsContainer.append(userIcon, form);

		form.append(this.items.render());
	}
}