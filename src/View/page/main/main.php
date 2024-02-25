<?php
$products = $this->getVariable('products');
$tags = $this->getVariable('tag');
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

	const mainList = new ProductList({
		attachToNodeId: 'product__list-container',
		items: <?= \Up\Util\Json::encode($products) ?>,
	});
	mainList.render();

	const tagList = new TagList({
		attachToNodeId: 'tags-list',
		items: <?= \Up\Util\Json::encode($tags) ?>,
	})

	tagList.render();

	const auth = new Auth({
		attachToNodeId: 'form-auth',
	})

	auth.render();
</script>

