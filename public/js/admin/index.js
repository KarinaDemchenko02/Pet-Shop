const buttonFormOpen = document.querySelectorAll('.table__button_edit');
const buttonFormClose = document.querySelectorAll('.form__close');
const formContent = document.querySelector('.form__box');

const buttonAdd = document.querySelector('.form__button_add');
const buttonEdit = document.querySelector('.form__button_change');

const inputFormTitle = document.getElementById('title');
const inputFormPrice = document.getElementById('price');
const inputFormDesc = document.getElementById('desc');
const inputFormId = document.getElementById('idProduct');
const inputFormIdDisabled = document.getElementById('idProductDisable');

const inputAction = document.querySelector('.form__input-action');
buttonFormOpen.forEach(btn => {
	btn.addEventListener('click', (event) => {

		buttonAdd.style.display = 'none';
		buttonEdit.style.display = 'block';

		inputAction.value = 'change';

		formContent.classList.add('open');

		const searchArea = event.target.parentNode.parentNode;

		const inputTitle = searchArea.querySelector('.table__th_title');

		const inputDesc = searchArea.querySelector('.table__th_desc');

		const inputPrice = searchArea.querySelector('.table__th_price');

		const inputId = searchArea.querySelector('.table__th_id');

		inputFormTitle.value = inputTitle.textContent;
		inputFormDesc.value = inputDesc.textContent;
		inputFormPrice.value = inputPrice.textContent;
		inputFormId.value = inputId.textContent;
	})
})

buttonFormClose.forEach(btn => {
	btn.addEventListener('click', () => {
		formContent.classList.remove('open');
	})
})

const buttonDeleteOpen = document.querySelectorAll('.table__button_delete');
const buttonDeleteClose = document.querySelectorAll('.delete__button-no');
const deleteContent = document.querySelector('.delete__box');

buttonDeleteOpen.forEach(btn => {
	btn.addEventListener('click', () => {
		deleteContent.classList.add('open');
		const searchArea = event.target.parentNode.parentNode;
		const inputId = searchArea.querySelector('.table__th_id');

		inputFormIdDisabled.value = inputId.textContent;
	})
})

buttonDeleteClose.forEach(btn => {
	btn.addEventListener('click', () => {
		deleteContent.classList.remove('open');
	})
})

const buttonAddOpen = document.querySelector('.table__button-add');

buttonAddOpen.addEventListener('click', () => {
	formContent.classList.add('open');

	buttonEdit.style.display = 'none';
	buttonAdd.style.display = 'block';

	inputAction.value = 'add';

	inputFormTitle.value = '';
	inputFormDesc.value = '';
	inputFormPrice.value = '';
	inputFormId.value = '';
})