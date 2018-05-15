<?php
namespace Pronamic\WordPress\Pay\Extensions\NinjaForms;

use Pronamic\WordPress\Pay\Payments\PaymentData as Pay_PaymentData;
use Pronamic\WordPress\Pay\Payments\Item;
use Pronamic\WordPress\Pay\Payments\Items;

/**
 * Title: WordPress pay Ninja Forms payment data
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Ruben Droogh
 */
class PaymentData extends Pay_PaymentData {

	/**
	 * Form ID
	 *
	 * @var string
	 */
	private $form_id;

	/**
	 * Action
	 *
	 * @var array
	 */
	private $form_data;

	/**
	 * Constructs and initializes an Formidable Forms payment data object.
	 *
	 * @param string  $entry_id
	 * @param string  $form_id
	 * @param WP_Post $action
	 */
	public function __construct( $form_id, $data ) {
		parent::__construct();

		$this->form_id   = $form_id;
		$this->form_data = $data;
	}

	/**
	 * Get items
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_items()
	 * @return Items
	 */
	public function get_items() {
		// Items
		$items = new Items();

		// Item
		// We only add one total item, because iDEAL cant work with negative price items (discount)
		$item = new Item();
		$item->setNumber( $this->get_order_id() );
		$item->setDescription( $this->get_description() );
		$item->setPrice( $this->get_amount_from_field() );
		$item->setQuantity( 1 );

		$items->addItem( $item );

		return $items;
	}

	/**
	 * Get payment method.
	 *
	 * @return string|null
	 */
	public function get_payment_method() {
		$payment_method = null;

		$payment_method_field = $this->data['pronamic_pay_payment_method_field'];

		if ( ! empty( $payment_method_field ) && isset( $this->entry->metas[ $payment_method_field ] ) ) {
			$payment_method = $this->entry->metas[ $payment_method_field ];

			$replacements = array(
				'pronamic_pay_' => '',
				'pronamic_pay'  => '',
			);

			$payment_method = strtr( $payment_method, $replacements );

			if ( empty( $payment_method ) ) {
				$payment_method = null;
			}
		}

		return $payment_method;
	}

	/**
	 * Get source indicator
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_source()
	 * @return string
	 */
	public function get_source() {
		return 'ninja-forms';
	}

	/**
	 * Get currency
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_currency_alphabetic_code()
	 * @return string
	 */
	public function get_currency_alphabetic_code() {
		return 'EUR';
	}

	/**
	 * Get description
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_description()
	 * @return string
	 */
	public function get_description() {
		$description = '';

		if ( isset( $this->form_data['description'] ) ) {
			$description = $this->form_data['description'];
		}

		return $description;
	}

	/**
	 * Get order ID
	 *
	 * @see Pronamic_Pay_PaymentDataInterface::get_order_id()
	 * @return string
	 */
	public function get_order_id() {
		return $this->entry_id;
	}

	/**
	 * Get amount
	 *
	 * @return float
	 */
	private function get_amount_from_field() {
		$amount = 0;

		if ( isset( $this->form_data['amount'] ) ) {
			$amount = $this->form_data['amount'];
		}

		return $amount;
	}
}

?>