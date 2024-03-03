<?php
$products = $this->getVariable('products');
$tags = $this->getVariable('tag');
$basketItem = $this->getVariable('basketItem');
$isLogin = $this->getVariable('isLogIn');
?>

<div class="main__container">
	<section class="tags">
		<div class="tags__container">
			<h2 class="tags__heading">Теги:</h2>
			<ul class="tags__list" id="tags-list">
			</ul>
		</div>
	</section>
	<section class="form" id="form-auth"></section>
	<section class="basket">
		<?php $this->getVariable('basket')->display() ?>
	</section>
	<section class="product">
		<div id="product__list-container"></div>
	</section>
</div>
<script type="module">
	import { ProductList } from "/js/main/product/product-list.js";
	import { TagList } from "/js/main/tag/tag-list.js";
	import {Auth} from "/js/main/auth/auth.js";
	import {Search} from "/js/main/product/search/search.js";

	const mainList = new ProductList({
		attachToNodeId: 'product__list-container',
		items: <?= \Up\Util\Json::encode($products) ?>,
		basketItem: <?= \Up\Util\Json::encode($basketItem) ?>,
	});
	mainList.render();

	const tagList = new TagList({
		attachToNodeId: 'tags-list',
		items: <?= \Up\Util\Json::encode($tags) ?>,
		basketItem: <?= \Up\Util\Json::encode($basketItem) ?>,
	})

	tagList.render();

	const auth = new Auth({
		attachToNodeId: 'form-auth',
		login: <?= \Up\Util\Json::encode($isLogin) ?>,
	})

	auth.render();

	const search = new Search({
		attachToNodeId: 'header-search',
		items: <?= \Up\Util\Json::encode($products) ?>,
		basketItem: []
	});

	search.render();

	if (window.location.search.includes('tag=')) {
		const urlParams = new URLSearchParams(window.location.search);
		const tags = urlParams.getAll('tag');

		tagList.handleFilterTagButtonClick({ id: tags });
	}

	if (window.location.search.includes('title=')) {
		const urlParams = new URLSearchParams(window.location.search);
		const title = urlParams.get('title');

		search.handleSearchButtonSubmit({ title: title });
	}
</script>

