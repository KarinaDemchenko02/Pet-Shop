export class UserItem
{
	id;
	name;
	email;
	phoneNumber;
	role;
	editButtonHandler;
	removeButtonHandler;
	restoreButtonHandler;

	constructor({ id, name, email, phoneNumber, role, editButtonHandler, removeButtonHandler, restoreButtonHandler })
	{
		this.id = Number(id);
		this.name = String(name);
		this.email = String(email);
		this.phoneNumber = String(phoneNumber);
		this.role = String(role);

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
		const containerInput = document.createElement('div');
		containerInput.classList.add('account__container-input');

		const labelRole = document.createElement('label');
		labelRole.innerText = 'Роль: ' + this.role;
		labelRole.classList.add('account__label', 'account__label-role');

		const labelName = document.createElement('label');
		labelName.classList.add('account__label');
		labelName.htmlFor = 'name'
		labelName.innerText = 'Имя';

		const inputName = document.createElement('input');
		inputName.classList.add('account__input');
		inputName.id = 'name';
		inputName.type = 'text';
		inputName.name = 'name';
		inputName.value = this.name;

		const labelPhone = document.createElement('label');
		labelPhone.classList.add('account__label');
		labelPhone.htmlFor = 'phone'
		labelPhone.innerText = 'Телефон';

		const inputPhone = document.createElement('input');
		inputPhone.classList.add('account__input');
		inputPhone.id = 'phone';
		inputPhone.type = 'text';
		inputPhone.name = 'phone';
		inputPhone.value = this.phoneNumber;

		const labelEmail = document.createElement('label');
		labelEmail.classList.add('account__label');
		labelEmail.htmlFor = 'email'
		labelEmail.innerText = 'email';

		const inputEmail = document.createElement('input');
		inputEmail.classList.add('account__input');
		inputEmail.id = 'email';
		inputEmail.type = 'text';
		inputEmail.name = 'email';
		inputEmail.value = this.email;

		const labelPassword = document.createElement('label');
		labelPassword.classList.add('account__label');
		labelPassword.htmlFor = 'password'
		labelPassword.innerText = 'password';

		const inputPassword = document.createElement('input');
		inputPassword.classList.add('account__input');
		inputPassword.id = 'password';
		inputPassword.type = 'password';
		inputPassword.name = 'password';

		const spinner = document.createElement('div');
		spinner.classList.add('spinner-border', 'text-light', 'spinner-edit');
		const spinnerLoading = document.createElement('span');
		spinnerLoading.innerText = 'Loading...';
		spinnerLoading.classList.add('visually-hidden');
		spinner.append(spinnerLoading);

		const buttonSubmit = document.createElement('button');
		buttonSubmit.classList.add('account__button');
		buttonSubmit.innerText = 'Сохранить изменения';
		buttonSubmit.id = 'save';
		buttonSubmit.name = 'saveAccount';
		buttonSubmit.addEventListener('click', this.handleEditButtonClick.bind(this));

		buttonSubmit.append(spinner);

		containerInput.append(labelRole, labelName, inputName, labelPhone, inputPhone, labelEmail, inputEmail, labelPassword, inputPassword, buttonSubmit);

		return containerInput;
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