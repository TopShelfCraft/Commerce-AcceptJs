<?php

namespace topshelfcraft\acceptjs\commerce;

use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\models\PaymentSource;


/**
 * @author    Top Shelf Craft (Michael Rog)
 * @package   acceptjs
 * @since     3.0.0
 */
class PaymentForm extends BasePaymentForm
{

	/*
	 * Properties
	 */

	/**
	 * @var string Data descriptor
	 */
	public $dataDescriptor = "COMMON.ACCEPT.INAPP.PAYMENT";

	/**
	 * @var string Data value
	 */
	public $dataValue;


	/*
	 * Public methods
	 */

	/**
	 * @param PaymentSource $paymentSource
	 */
	public function populateFromPaymentSource(PaymentSource $paymentSource)
	{
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['dataDescriptor', 'dataValue'], 'required'],
			[['dataDescriptor'], 'string'],
			[['dataValue'], 'string'],
		];
	}

}
