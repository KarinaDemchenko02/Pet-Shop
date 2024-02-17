export default function pagination()
{
	const buttonPagination = document.querySelectorAll('.pagination__button');
	const url_string = window.location.href;
	const url = new URL(url_string);
	const paramValue = url.searchParams.get("page");
	buttonPagination.forEach(btn => {
		if (paramValue === null || parseInt(paramValue) < 1) {
			if (parseInt(btn.textContent) === 1)
			{
				btn.classList.add('is-active');
			}
		}
		else
		{
			if (parseInt(btn.textContent) === parseInt(paramValue)) {
				btn.classList.add('is-active');
			}
		}
	})
}