<?php
$userList = [
	'id' => $this->getVariable('user')['id'],
	'role' => $this->getVariable('user')['role'],
	'email' => $this->getVariable('user')['email'],
	'name' => $this->getVariable('user')['name'],
	'surname' => $this->getVariable('user')['surname'],
	'phoneNumber' => $this->getVariable('user')['phoneNumber'],
];

$orders = $this->getVariable('orders');
?>

<div class="account">
	<div class="account__container" id="account-item"></div>
</div>

<script type="module">
	import { UserList } from "/js/main/user/user-list.js";

	const mainProductList = new UserList({
		attachToNodeId: 'account-item',
		items: <?= \Up\Util\Json::encode($userList) ?>,
		orders: <?= \Up\Util\Json::encode($orders) ?>,
	});

	mainProductList.render();
</script>
