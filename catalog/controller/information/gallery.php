<?phpclass ControllerInformationGallery extends Controller {	public function index() {		$this->load->language('information/gallery');		$this->data['breadcrumbs'] = array();		      	$this->data['breadcrumbs'][] = array(        	'text'      => $this->language->get('text_home'),			'href'      => $this->url->link('common/home'),        	'separator' => false      	);		$this->data['breadcrumbs'][] = array(			'href'      => $this->url->link('information/gallery'),			'text'      => $this->language->get('heading_title'),			'separator' => $this->language->get('text_separator')		);		$this->document->addScript('catalog/view/javascript/prettyphoto/prettyphoto/jquery.prettyPhoto.min.js');		$this->document->addStyle('catalog/view/javascript/prettyphoto/prettyphoto/prettyPhoto-min.css');		$this->data['text_gallery'] = $this->language->get('text_gallery');		$this->data['text_copyright'] = $this->language->get('text_copyright');		$this->load->model('catalog/gallery');				$this->load->model('tool/image');		if (isset($this->request->get['album'])) {			$album = '';			$parts = explode('_', $this->request->get['album']);			foreach ($parts as $album_id) {				$gallery_info = $this->model_catalog_gallery->getGallery($album_id);				if (!$album) {					$album = $album_id;				} else {					$album .= '_' . $album_id;				}				$this->data['breadcrumbs'][] = array(					'href'      => $this->url->link('information/gallery&album=' . $album),					'text'      => html_entity_decode($gallery_info['name']),					'separator' => $this->language->get('text_separator')				);			}												$gallery_id = array_pop($parts);			$gallery_info = $this->model_catalog_gallery->getGallery($gallery_id);			if ($gallery_info) {				$this->getGalleries($gallery_id);			} else {				$this->getError();			}		} else {			$this->getGalleries(0);		}	}	private function getGalleries($gallery_id) {			$gallery_info = $this->model_catalog_gallery->getGallery($gallery_id);		$gallery_data = $this->model_catalog_gallery->getGalleries($gallery_id);		if ($gallery_data) {			$this->document->setTitle($this->language->get('heading_title'));			if ($gallery_info) {				$this->data['heading_title'] = html_entity_decode($gallery_info['name']);				$this->data['description'] = html_entity_decode($gallery_info['description']);			} else {				$this->data['heading_title'] = $this->language->get('heading_title');				$this->data['description'] = $this->language->get('text_description');			}			$gallery_total = $this->model_catalog_gallery->getTotalGalleriesByGalleryId($gallery_id);			if ($gallery_total) {				$this->data['description'] = $this->language->get('text_description');				$this->data['galleries'] = array();				$this->data['gallery_id'] = $gallery_id;				foreach ($this->model_catalog_gallery->getGalleries($gallery_id) as $result) {					if (isset($this->request->get['album'])) {						$href = $this->url->link('information/gallery&album=' . $this->request->get['album'] . '_' . $result['gallery_id']);					} else {						$href = $this->url->link('information/gallery&album=' . $result['gallery_id']);					}					$this->data['galleries'][] = array(						'name'  => html_entity_decode($result['name']),						'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_gallery_thumb_width'), $this->config->get('config_gallery_thumb_height')),						'href'  => $href					);				}				$image_total = $this->model_catalog_gallery->getTotalImagesByGalleryId($gallery_id);				if ($image_total) {					$this->data['text_images'] = $this->language->get('text_images');					$this->data['text_enlarge'] = $this->language->get('text_enlarge');					if (isset($this->request->get['page'])) {						$page = $this->request->get['page'];					} else {						$page = 1;					}					$this->data['images'] = array();					$results = $this->model_catalog_gallery->getGalleryImages($gallery_id, ($page - 1) * $this->config->get('config_gallery_limit'), $this->config->get('config_gallery_limit'));					foreach ($results as $result) {						if ($result['image']) {							$image = $result['image'];						} else {							$image = 'no_image.jpg';						}						if (trim($result['image_title'])) {							$image_title = $result['image_title'];						} else {							$image_file = explode('/', $image);							$image_name = explode('.', $image_file[1]);							$image_title = ucwords(str_replace('_', ' ', $image_name[0]));						}						$this->data['images'][] = array(							'title' => $image_title,							'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_gallery_popup_width'), $this->config->get('config_gallery_popup_height')),							'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_gallery_thumb_width'), $this->config->get('config_gallery_thumb_height'))						);					}				}				$url = '';				if (isset($this->request->get['page'])) {					$url .= '&page=' . $this->request->get['page'];				}				if (isset($page)) {					$pagination = new Pagination();					$pagination->total = $image_total;					$pagination->page = $page;					$pagination->limit = $this->config->get('config_gallery_limit');					$pagination->text = $this->language->get('text_pagination');					$pagination->url = $this->url->link('information/gallery&album=' . $this->request->get['album'] . $url . '&page={page}');					$this->data['pagination'] = $pagination->render();				}				if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/gallery.tpl')) {					$this->template = $this->config->get('config_template') . '/template/information/gallery.tpl';				} else {					$this->template = 'default/template/information/gallery.tpl';				}				$this->children = array(				'common/column_left',				'common/column_right',				'common/content_top',				'common/content_bottom',				'common/footer',				'common/header'			);				$this->response->setOutput($this->render());			} else {				$this->getError();			}		} else {			$this->getGallery($gallery_id);		}	}	private function getGallery($gallery_id) {		$gallery_info = $this->model_catalog_gallery->getGallery($gallery_id);		if ($gallery_info) {			$this->document->setTitle(html_entity_decode($gallery_info['name']));			$this->data['heading_title'] = html_entity_decode($gallery_info['name']);			$this->data['description'] = html_entity_decode($gallery_info['description']);			$this->data['gallery_id'] = $gallery_id;			$gallery_total = $this->model_catalog_gallery->getTotalGalleriesByGalleryId($gallery_id);			if ($gallery_total) {				$this->getGalleries($gallery_id);			}			$image_total = $this->model_catalog_gallery->getTotalImagesByGalleryId($gallery_id);			if ($image_total) {				$this->data['text_images'] = $this->language->get('text_images');				$this->data['text_enlarge'] = $this->language->get('text_enlarge');				if (isset($this->request->get['page'])) {					$page = $this->request->get['page'];				} else {					$page = 1;				}				$this->data['images'] = array();				$results = $this->model_catalog_gallery->getGalleryImages($gallery_id, ($page - 1) * $this->config->get('config_gallery_limit'), $this->config->get('config_gallery_limit'));				foreach ($results as $result) {					if ($result['image']) {						$image = $result['image'];					} else {						$image = 'no_image.jpg';					}					if (trim($result['image_title'])) {						$image_title = $result['image_title'];					} else {						$image_file = explode('/', $image);						$image_name = explode('.', $image_file[1]);						$image_title = ucwords(str_replace('_', ' ', $image_name[0]));					}					$this->data['images'][] = array(						'title' => $image_title,						'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('config_gallery_popup_width'), $this->config->get('config_gallery_popup_height')),						'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_gallery_thumb_width'), $this->config->get('config_gallery_thumb_height'))					);				}			}			$url = '';			if (isset($this->request->get['page'])) {				$url .= '&page=' . $this->request->get['page'];			}			if (isset($page)) {				$pagination = new Pagination();				$pagination->total = $image_total;				$pagination->page = $page;				$pagination->limit = $this->config->get('config_gallery_limit');				$pagination->text = $this->language->get('text_pagination');				$pagination->url = $this->url->link('information/gallery&album=' . $this->request->get['album'] . $url . '&page={page}');				$this->data['pagination'] = $pagination->render();			}			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/gallery.tpl')) {				$this->template = $this->config->get('config_template') . '/template/information/gallery.tpl';			} else {				$this->template = 'default/template/information/gallery.tpl';			}			$this->children = array(				'common/column_left',				'common/column_right',				'common/content_top',				'common/content_bottom',				'common/footer',				'common/header'			);			$this->response->setOutput($this->render());		} else {			$this->getError();		}	}	private function getError() {		if (isset($this->request->get['album'])) {			$href = $this->url->link('information/gallery&album=' . $this->request->get['album']);		} else {			$href = $this->url->link('information/gallery');		}		$this->document->breadcrumbs[] = array(			'href'      => $href,			'text'      => $this->language->get('text_error'),			'separator' => $this->language->get('text_separator')		);		$this->document->setTitle = $this->language->get('text_error');		$this->data['heading_title'] = $this->language->get('text_error');		$this->data['text_error'] = $this->language->get('text_error');		$this->data['button_continue'] = $this->language->get('button_continue');		$this->data['continue'] = $this->url->link('common/home');		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/error/not_found.tpl')) {			$this->template = $this->config->get('config_template') . '/template/error/not_found.tpl';		} else {			$this->template = 'default/template/error/not_found.tpl';		}		$this->children = array(				'common/column_left',				'common/column_right',				'common/content_top',				'common/content_bottom',				'common/footer',				'common/header'			);		$this->response->setOutput($this->render());	}}?>