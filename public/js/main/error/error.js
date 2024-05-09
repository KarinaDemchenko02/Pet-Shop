export class Error
{
	messages;
	container;
	href;
	constructor(messages, container = null, href = '/') {
		this.messages = messages;
		this.container = container;
		this.href = href;
	}

	printError()
	{
		const containerError = document.createElement('div');

		containerError.classList.add('form__alert-container')

		const errorHeading = document.createElement('h2');
		errorHeading.classList.add('form__alert-heading')

		errorHeading.innerText = this.messages;

		containerError.append(errorHeading);

		this.container.append(containerError);
	}
	render()
	{
		const container = document.createElement('div');
		container.classList.add('product__error');

		const errorHeading = document.createElement('h2');
		errorHeading.innerText = this.messages;
		errorHeading.classList.add('product__error-heading');

		const linkMainPage = document.createElement('a');
		linkMainPage.classList.add('product__error-link');
		linkMainPage.innerText = 'Перейти на главную';
		linkMainPage.href = this.href;

		container.append(errorHeading, linkMainPage);

		return container;
	}
}