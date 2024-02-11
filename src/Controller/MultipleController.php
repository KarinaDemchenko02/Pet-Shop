<?php

namespace Up\Controller;

class MultipleController
{
	public function processAction(): bool
	{
		if (isset($_POST['action'])) {
			if ($_POST['action'] === 'disable')
			{
				$disable = new DisableController();
				$disable->disableAction();

				return true;
			}

			if ($_POST['action'] === 'change') {
				$change = new ChangeController();
				$change->changeAction();

				return true;
			}

			if ($_POST['action'] === 'add') {
				$add = new AddController();
				$add->addAction();

				return true;
			}
		}

		return true;
	}
}