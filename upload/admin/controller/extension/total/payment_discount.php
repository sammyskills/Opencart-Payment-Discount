<?php 
/**
 * Payment Discount Extension for OC 3.0.2.0
 *
 * @package Opencart 3
 * @category Order Total Extension
 * @author Samuel Asor
 * @link https://github.com/sammyskills/opencart-payment-discount
 */
class ControllerExtensionTotalPaymentDiscount extends Controller
{
	
	private $error = array();

	public function index()
	{
		/* Page Title */
		$this->load->language('extension/total/payment_discount');
		$this->document->setTitle($this->language->get('heading_title_plain'));

		$this->load->model('setting/setting');

		/* Process form submission only if form is submitted via POST and
		 * validation is passed
		*/
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->model_setting_setting->editSetting('total_payment_discount', $this->request->post);

			// Store success message in session
			$this->session->data['success'] = $this->language->get('text_success');

			// Redirect URL
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true));
		}

		$data = array();

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true),
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_plain'),
			'href' => $this->url->link('extension/total/payment_discount', 'user_token=' . $this->session->data['user_token'], true),
		);

		// Text
		$data['heading_title'] = $this->language->get('heading_title_plain');
		$data['text_edit'] = $this->language->get('text_edit');

		// Total's current position (sort order)
		$data['total_position'] = 'TOTAL is currently at position ' . $this->config->get('total_total_sort_order') . '.';

		/*
		 * Get data for installed payment modules
		*/
		$this->load->model('setting/extension');
		$payment_extensions = $this->model_setting_extension->getInstalled('payment');
		$data['payment_methods'] = array();

		foreach ($payment_extensions as $payment_module) {
			if (is_file(DIR_APPLICATION . 'controller/extension/payment/' . $payment_module . '.php')) {
				$this->load->language('extension/payment/' . $payment_module);
				$data['payment_methods'][] = array(
					'name' => $this->language->get('heading_title'),
					'code' => $payment_module,
				);
			}
		}

		/*
		 * Process form fields (get details(name))
		*/
		// Status
		if (isset($this->request->post['total_payment_discount_status'])) {
			$data['total_payment_discount_status'] = $this->request->post['total_payment_discount_status'];
		}
		else {
			$data['total_payment_discount_status'] = $this->config->get('total_payment_discount_status');
		}

		// Sort order
		if (isset($this->request->post['total_payment_discount_sort_order'])) {
			$data['total_payment_discount_sort_order'] = $this->request->post['total_payment_discount_sort_order'];
		}
		else {
			$data['total_payment_discount_sort_order'] = $this->config->get('total_payment_discount_sort_order');
		}

		// Payment Type
		if (isset($this->request->post['total_payment_discount_payment_type'])) {
			$data['total_payment_discount_payment_type'] = $this->request->post['total_payment_discount_payment_type'];
		}
		else {
			$data['total_payment_discount_payment_type'] = $this->config->get('total_payment_discount_payment_type');
		}

		// Discount Percentage
		if (isset($this->request->post['total_payment_discount_percentage'])) {
			$data['total_payment_discount_percentage'] = $this->request->post['total_payment_discount_percentage'];
		}
		else {
			$data['total_payment_discount_percentage'] = $this->config->get('total_payment_discount_percentage');
		}

		// Description Text
		if (isset($this->request->post['total_payment_discount_description'])) {
			$data['total_payment_discount_description'] = $this->request->post['total_payment_discount_description'];
		}
		else {
			$data['total_payment_discount_description'] = $this->config->get('total_payment_discount_description');
		}

		// Buttons
		$data['action']['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);
		$data['action']['save'] = $this->url->link('extension/total/payment_discount', 'user_token=' . $this->session->data['user_token'], true);

		// Error
		$data['error'] = $this->error;

		// Commons
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		// Output
		$html_output = $this->load->view('extension/total/payment_discount', $data);
		$this->response->setOutput($html_output);
	}

	public function validate()
	{
		// Permission
		if (!$this->user->hasPermission('modify', 'extension/total/payment_discount')) {
			$this->error['permission'] = true;
			return false;
		}

		// Sort order
		if (!is_numeric($this->request->post['total_payment_discount_sort_order'])) {
			$this->error['sort_order'] = true;
			// return false;
		}

		// Payment type
		if (empty($this->request->post['total_payment_discount_payment_type'])) {
			$this->error['payment_type'] = true;
		}

		// Discount Percentage
		if (!is_numeric($this->request->post['total_payment_discount_percentage'])) {
			$this->error['discount_percentage'] = true;
		}

		// Descriptive text
		if (empty($this->request->post['total_payment_discount_description'])) {
			$this->error['descriptive_text'] = true;
		}

		return empty($this->error);
	}

}