export class ChangeForm
{
	acceptEditButtonHandler;
	closeEditButtonHandler;

	formBox;
	/*id;
	title;
	desc;
	price;
	tags;*/
	constructor(/*id, title, desc, price, tags, */acceptEditButtonHandler, closeEditButtonHandler)
	{
		/*this.id = Number(id);
		this.title = String(title);
		this.description = String(desc);
		this.price = Number(price);
		this.tags = String(tags);*/

		if (typeof acceptEditButtonHandler === 'function')
		{
			this.acceptEditButtonHandler = acceptEditButtonHandler;
		}

		if (typeof closeEditButtonHandler === 'function')
		{
			this.closeEditButtonHandler = closeEditButtonHandler;
		}
	}

	render(/*{id, title, desc, price, tags}*/)
	{
		const formBox = document.createElement('div');
		formBox.classList.add('form__box');

		const formContainer = document.createElement('div');
		formContainer.classList.add('form__container');

		const closeButton = document.createElement('button');
		closeButton.classList.add('form__close');
		closeButton.addEventListener('click', this.handleCloseEditButtonClick.bind(this))
		const closeIcon = document.createElement('i');
		closeIcon.classList.add('form__close-icon', 'material-icons');
		closeIcon.innerText = 'close';
		closeButton.append(closeIcon);

		const form = document.createElement('div');
		form.classList.add('form');


		const titleLabel = document.createElement('label');
		titleLabel.classList.add('form__label');
		titleLabel.htmlFor = 'title';
		titleLabel.innerText = 'Название';

		const titleInput = document.createElement('input');
		titleInput.classList.add('form__input');
		titleInput.id = 'title';
		titleInput.type = 'text';
		titleInput.name = 'title';
		// titleInput.value = title;


		const descLabel = document.createElement('label');
		descLabel.classList.add('form__label');
		descLabel.htmlFor = 'desc';
		descLabel.innerText = 'Описание';

		const descInput = document.createElement('input');
		descInput.classList.add('form__input');
		descInput.id = 'desc';
		descInput.type = 'text';
		descInput.name = 'desc';
		// descInput.value = desc;


		const priceLabel = document.createElement('label');
		priceLabel.classList.add('form__label');
		priceLabel.htmlFor = 'price';
		priceLabel.innerText = 'Цена';

		const priceInput = document.createElement('input');
		priceInput.classList.add('form__input');
		priceInput.id = 'price';
		priceInput.type = 'text';
		priceInput.name = 'price';
		// priceInput.value = price;


		const tagsLabel = document.createElement('label');
		tagsLabel.classList.add('form__label');
		tagsLabel.htmlFor = 'tags';
		tagsLabel.innerText = 'Теги';

		const tagsInput = document.createElement('input');
		tagsInput.classList.add('form__input');
		tagsInput.id = 'tags';
		tagsInput.type = 'text';
		tagsInput.name = 'tags';
		// tagsInput.value = tags;

		const acceptButton = document.createElement('button');
		acceptButton.classList.add('form__button','form__button_change');
		acceptButton.id = 'changed';
		acceptButton.type = 'submit';
		acceptButton.name = 'changeProduct';
		acceptButton.innerText = 'Редактировать';
		acceptButton.addEventListener('click', this.handleAcceptEditButtonClick.bind(this))

		form.append(titleLabel, titleInput, descLabel, descInput,
			priceLabel, priceInput, tagsLabel, tagsInput, acceptButton);
		formContainer.append(closeButton, form);
		formBox.append(formContainer);

		this.formBox = formBox;
		return this.formBox;
	}

	handleAcceptEditButtonClick()
	{
		if (this.acceptEditButtonHandler)
		{
			this.acceptEditButtonHandler(this);
		}
	}

	handleCloseEditButtonClick()
	{
		if (this.closeEditButtonHandler)
		{
			this.closeEditButtonHandler(this);
		}
	}
}
