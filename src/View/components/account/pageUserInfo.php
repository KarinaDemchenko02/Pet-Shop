<?php
$userList = [
	'id' => $this->getVariable('user')['id'],
	'role' => $this->getVariable('user')['role'],
	'email' => $this->getVariable('user')['email'],
	'name' => 'test',
	'phoneNumber' => $this->getVariable('user')['phoneNumber'],
];
?>

<div class="account">
	<div class="account__container" id="account-item"></div>
</div>

<script type="module">
	import { UserList } from "/js/main/user/user-list.js";

	const mainProductList = new UserList({
		attachToNodeId: 'account-item',
		items: <?= \Up\Util\Json::encode($userList) ?>,
	});

	mainProductList.render();
</script>
