$(document).ready(function () {
	$("#changed").on("click", function () {
		let id = $("#idProduct").val();
		let title = $("#title").val().trim();
		let description = $("#desc").val().trim();
		let price = $("#price").val().trim();
		let action = $("#action").val();

		if (title === "") {
			$("#error").text("Поле title не может быть пустым");
			return false;
		} else if (description === "") {
			$("#error").text("Поле description не может быть пустым");
			return false;
		} else if (price === "") {
			$("#error").text("Поле price не может быть пустым");
			return false;
		}

		$("#error").text("");

		$.ajax({
			url: '/QueryProcessing.php',
			type: 'POST',
			cache: false,
			data: {
				'id': id,
				'title': title,
				'description': description,
				'price': price,
				'action': action,
			},
			dataType: 'html',
			beforeSend: function () {
				$("#information").text('Загрузка');
				$("#changed").prop("disabled", true);
			},
			success: function (data) {
				if(data === 'error') {
					console.log('Ошибка: данные не получены на сервере');
					return false;
				}
				$("#changed").prop("disabled", false);
				$("#information").text(data);
			}
		});
	})
})
