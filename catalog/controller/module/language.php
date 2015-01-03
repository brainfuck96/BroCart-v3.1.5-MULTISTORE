<?php  
class ControllerModuleLanguage extends Controller {
	protected function index() {
    	if (isset($this->request->post['language_code'])) {
			$this->session->data['language'] = $this->request->post['language_code'];

			if (isset($this->request->post['redirect'])) {
				$this->redirect($this->request->post['redirect']);
			} else {
				$this->redirect($this->url->link('common/home'));
			}
    	}

		$this->language->load('module/language');

		$this->data['text_language'] = $this->language->get('text_language');

		$this->data['action'] = $this->url->link('module/language');

		$this->data['language_code'] = $this->session->data['language'];

		$this->load->model('localisation/language');

		$this->data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$this->data['languages'][] = array(
					'name'  => $result['name'],
					'code'  => $result['code'],
					'image' => $result['image']
				);
			}
		}
if ($this->config->get('config_seo_url')) {
		if (!isset($this->request->get['route'])) {
			foreach($this->data['languages'] as $i => $language) {
				$this->data['languages'][$i]['redirect'] = $this->url->link('common/home', '', 'NONSSL', $language['code']);
			}
		} else {
			$data = $this->request->get;

			unset($data['_route_']);

			$route = $data['route'];

			unset($data['route']);

			$url = '';

			if ($data) {
				$url = '&' . urldecode(http_build_query($data, '', '&'));
			}

			if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
				$connection = 'SSL';
			} else {
				$connection = 'NONSSL';
			}
			foreach($this->data['languages'] as $i => $language) {
				$this->data['languages'][$i]['redirect'] = $this->url->link($route, $url, $connection, $language['code']);
			}
		}
} else {
		if (!isset($this->request->get['route'])) {
			foreach($this->data['languages'] as $i => $language) {
				$this->data['languages'][$i]['redirect'] = $this->url->link('common/home', '', 'NONSSL');
			}
		} else {
			$data = $this->request->get;
			
			unset($data['_route_']);
			
			$route = $data['route'];
			
			unset($data['route']);
			
			$url = '';
			
			if ($data) {
				$url = '&' . urldecode(http_build_query($data, '', '&'));
			}	
			
			if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
				$connection = 'SSL';
			} else {
				$connection = 'NONSSL';
			}
			
			foreach($this->data['languages'] as $i => $language) {
				$this->data['languages'][$i]['redirect'] = $this->url->link($route, $url, $connection);
			}
		}
}
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/language.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/language.tpl';
		} else {
			$this->template = 'default/template/module/language.tpl';
		}
		
		$this->render();
	}
}
?>