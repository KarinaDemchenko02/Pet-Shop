<header class="header">
	<div class="header__top-menu">
		<img class="header__logo" src="/../compressImages/logoMadagascar.png" alt="logo">
		<div class="header__form" id="header-search"></div>
		<div class="header__phone">
			<i class="header__call material-icons">call</i>
			<a class="header__link-phone" href="tel:+79521132415">+7 (952) 113-24-15</a>
		</div>
	</div>
	<div class="header__lower-menu">
		<nav class="header__nav">
			<ul class="header__list">
				<li class="header__item">
					<a class="header__link" href="/">
						<svg width="30px" height="30px" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path clip-rule="evenodd"
								  d="M16.3382 1.94393L25.9705 9.82424L26.0201 9.8788C26.1701 10.0437 26.3998 10.3064 26.5943 10.6198C26.7798 10.9189 27 11.3686 27 11.8956V24.9976C27 26.1013 26.1068 27 25 27H18.7601C17.9317 27 17.2601 26.3284 17.2601 25.5V20.7939C17.2601 18.9948 15.8058 17.5405 14.0168 17.5405C12.2279 17.5405 10.7735 18.9948 10.7735 20.7939V25.5C10.7735 26.3284 10.102 27 9.27354 27H3C1.89318 27 1 26.1013 1 24.9976V11.7425C1 11.0901 1.36299 10.564 1.56986 10.3028C1.69049 10.1505 1.80873 10.0264 1.89631 9.94036C1.9407 9.89677 1.97877 9.86147 2.0074 9.83565C2.02175 9.8227 2.03384 9.81204 2.0433 9.80382L2.05551 9.79329L2.06007 9.7894L2.06278 9.7871C2.06278 9.7871 2.06356 9.78646 2.7075 10.5515L2.06356 9.78646L2.07352 9.77807L11.6288 1.94617C12.9452 0.685478 15.0206 0.684487 16.3382 1.94393ZM3.35246 11.3159L3.3468 11.3209C3.33673 11.33 3.31953 11.3459 3.29759 11.3674C3.25251 11.4117 3.19388 11.4736 3.13764 11.5446C3.07966 11.6178 3.038 11.6834 3.01374 11.7344C3.00661 11.7494 3.00238 11.7602 3 11.767V24.9976L3.00006 24.9992L3.0007 25H8.77354V20.7939C8.77354 17.8948 11.1188 15.5405 14.0168 15.5405C16.9149 15.5405 19.2601 17.8948 19.2601 20.7939V25H24.9993L24.9999 24.9992L25 24.9976V11.8956C25 11.8989 25.0008 11.8992 25 11.8956C24.9966 11.8812 24.9788 11.8095 24.8948 11.6742C24.8108 11.5389 24.7005 11.4037 24.588 11.2772L15.004 3.43645L14.9714 3.40439C14.4228 2.86484 13.5451 2.86525 12.997 3.40534L12.9644 3.43744L3.35246 11.3159Z"
								  fill="#ffff" fill-rule="evenodd"/>
						</svg>
						<span class="header__link-name">Главная</span>
					</a>
				</li>
				<li class="header__item">
					<a class="header__link" href="/account/">
						<svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M5 21C5 17.134 8.13401 14 12 14C15.866 14 19 17.134 19 21M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z"
								stroke="#ffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<span class="header__link-name">Личный кабинет</span>
					</a>
				</li>
				<li class="header__item">
					<button id="buttonBasket" class="header__link header__button-item_basket">
						<svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path
								d="M6.29977 5H21L19 12H7.37671M20 16H8L6 3H3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z"
								stroke="#ffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<span class="header__basket-number"><?= count($_SESSION['shoppingSession']->getProducts())?></span>
						<span class="header__link-name">Корзина</span>
					</button>
				</li>
			</ul>
			<div id="header__auth"></div>
		</nav>
	</div>
</header>
