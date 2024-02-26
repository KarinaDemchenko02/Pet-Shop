<?php
$contentName = $this->getVariable('contentName');
$content = $this->getVariable('content');
$columns = $this->getVariable('columns');
$tags = $this->getVariable('tag');

?>

<div class="table__container">

	<div id="item-list">
	</div>

	<div class="delete__box">
		<?php $this->getVariable('delete')->display(); ?>
	</div>

</div>

<script type="module">
	import { ProductList } from "/js/admin/product/product-list.js";
	import { OrderList } from "/js/admin/order/order-list.js";
	import {TagList} from "/js/admin/tag/tag-list.js";
	if ('<?=$contentName?>' === 'products')
	{
		const mainList = new ProductList({
			attachToNodeId: 'item-list',
			items: <?= \Up\Util\Json::encode($content) ?>,
			columns: <?= \Up\Util\Json::encode($columns) ?>,
			tags: <?= \Up\Util\Json::encode($tags) ?>,
		});
		mainList.render();
	}
	if ('<?=$contentName?>' === 'orders')
	{
		console.log(<?=\Up\Util\Json::encode($columns)?>);
		const mainList = new OrderList({
			attachToNodeId: 'item-list',
			items: <?= \Up\Util\Json::encode($content) ?>,
			columns: <?= \Up\Util\Json::encode($columns) ?>,
		});
		mainList.render();
	}
	if ('<?=$contentName?>' === 'tags')
	{
		const mainList = new TagList({
			attachToNodeId: 'item-list',
			items: <?= \Up\Util\Json::encode($content) ?>,
			columns: <?= \Up\Util\Json::encode($columns) ?>,
		});
		mainList.render();
	}

</script>
