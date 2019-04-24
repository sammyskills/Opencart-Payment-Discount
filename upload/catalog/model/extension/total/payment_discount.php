<?php 
/**
 * 
 */
class ModelExtensionTotalPaymentDiscount extends Controller
{
	
	public function getTotal($total)
	{
		if ($this->config->get('total_payment_discount_status') && isset($this->session->data['payment_method']['code']) && $this->cart->getSubTotal()) {
			$discount_payment_method = $this->config->get('total_payment_discount_payment_type');

			if ($discount_payment_method == $this->session->data['payment_method']['code']) {
				$desc_text = $this->config->get('total_payment_discount_description');
				$discount = $this->config->get('total_payment_discount_percentage');
				$sort_order = $this->config->get('total_payment_discount_sort_order');
				
				$total['totals'][] = array(
					'code' => 'payment_discount',
					'title' => $desc_text,
					'value' => (($discount / 100) * $total['total']),
					'sort_order' => $sort_order,
				);

				$total['total'] -= (($discount / 100) * $total['total']);
			}
		}
	}
}