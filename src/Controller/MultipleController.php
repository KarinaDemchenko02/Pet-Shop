<?php

namespace Up\Controller;

class MultipleController
{
	public function processAction(): void
	{
		if (isset($_POST['action']))
		{
			if ($_POST['action'] === 'disable')
			{
				$disable = new DisableController();
				$disable->disableAction();
			}

			if ($_POST['action'] === 'change')
			{
				$change = new ChangeController();
				$change->changeAction();
			}

			if ($_POST['action'] === 'add')
			{
				$add = new AddController();
				$add->addAction();
			}
		}
		header('Location: /admin/');
	}
}
