<?php


namespace Up\Controller;

use Up\Exceptions\Service\AdminService\ProductNotDisable;
use Up\Service\ProductService\ProductService;
use Up\Util\Json;
use Up\Util\TemplateEngine\PageAdminTemplateEngine;

class RestoreController extends BaseController
{
	/*private Disable $disableService;*/
	public function __construct()
	{
		$this->engine = new PageAdminTemplateEngine();
		/*$this->disableService = new Disable();*/
	}

	public function restoreAction(): void
	{
		$data = Json::decode(file_get_contents("php://input"));

		$response = [];
		try {
			ProductService::restoreProduct((int)$data['id']);
			$result = true;
		} catch (ProductNotDisable) {
			$result = false;
		}

		$response['result'] = $result;

		if ($result) {
			$response['errors'] = [];
			http_response_code(200);
		} else {
			$response['errors'] = 'Товар не удалён';
			http_response_code(400);
		}
		echo Json::encode([
			'result' => 'Y'
		]);
	}
}