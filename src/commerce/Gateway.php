<?php

namespace topshelfcraft\acceptjs\commerce;

use Craft;
use craft\commerce\base\Gateway as BaseGateway;
use craft\commerce\base\RequestResponseInterface;
use craft\commerce\errors\NotImplementedException;
use craft\commerce\models\payments\BasePaymentForm;
use craft\commerce\models\PaymentSource;
use craft\commerce\models\Transaction;
use craft\web\Response as WebResponse;
use craft\web\View;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CustomerAddressType;
use net\authorize\api\contract\v1\CustomerDataType;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\OpaqueDataType;
use net\authorize\api\contract\v1\OrderType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\SettingType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\contract\v1\UserFieldType;
use net\authorize\api\controller\CreateTransactionController;
use topshelfcraft\acceptjs\Acceptjs;


/**
 * @author    Top Shelf Craft (Michael Rog)
 * @package   acceptjs
 * @since     3.0.0
 */
class Gateway extends BaseGateway
{

	private $_apiLoginId;
	private $_transactionKey;
	private $_environment;

	/*
	 * Public methods
	 */

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		$settings = Acceptjs::$plugin->getSettings();

		$this->_apiLoginId = $settings->apiLoginId;
		$this->_transactionKey = $settings->transactionKey;

		$this->_environment = $settings->environment;
		if ($settings->environment == 'PRODUCTION')
		{
			$this->_environment = ANetEnvironment::PRODUCTION;
		}
		elseif ($settings->environment == 'SANDBOX')
		{
			$this->_environment = ANetEnvironment::SANDBOX;
		}

	}

	/**
	 * @inheritdoc
	 */
	public function getPaymentFormHtml(array $params)
	{

		$view = Craft::$app->getView();

		$defaults = [
			'paymentForm' => $this->getPaymentFormModel()
		];
		$params = array_merge($defaults, $params);

		$previousMode = $view->getTemplateMode();
		$view->setTemplateMode(View::TEMPLATE_MODE_CP);
		$html = Craft::$app->getView()->renderTemplate('credits/_components/commerce/_gatewayFields', $params);
		$view->setTemplateMode($previousMode);

		return $html;

	}

	/**
	 * @inheritdoc
	 */
	public static function displayName(): string
	{
		return "Accept.js";
	}

	/**
	 * @inheritdoc
	 */
	public function getPaymentFormModel(): BasePaymentForm
	{
		return new PaymentForm();
	}

	/**
	 * @inheritdoc
	 */
	public function authorize(Transaction $transaction, BasePaymentForm $form): RequestResponseInterface
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function capture(Transaction $transaction, string $reference): RequestResponseInterface
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function completeAuthorize(Transaction $transaction): RequestResponseInterface
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function completePurchase(Transaction $transaction): RequestResponseInterface
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function createPaymentSource(BasePaymentForm $sourceData, int $userId): PaymentSource
	{
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function deletePaymentSource($token): bool
	{
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function purchase(Transaction $transaction, BasePaymentForm $form): RequestResponseInterface
	{
		/** @var PaymentForm $form */

		$order = $transaction->getOrder();
		// TODO: What if $order is `null`?

		/*
		 * Create a merchantAuthenticationType object with authentication details from the config.
		 */
		$merchantAuthentication = new MerchantAuthenticationType();
		$merchantAuthentication->setName($this->_apiLoginId);
		$merchantAuthentication->setTransactionKey($this->_transactionKey);

		/*
		 * Create the transaction refId
		 * This has a MaxSize of 20 per AnetApiSchema.xsd
		 */
		$refId = $order->id . ':' . time(true);

		/*
		 * Create the payment object for a payment nonce.
		 */
		$opaqueData = new OpaqueDataType();
		$opaqueData->setDataDescriptor($form->dataDescriptor);
		$opaqueData->setDataValue($form->dataValue);

		/*
		 * Add the payment data to a paymentType object
		 */
		$paymentOne = new PaymentType();
		$paymentOne->setOpaqueData($opaqueData);

		/*
		 * Create order information
		 */
		$gatewayOrder = new OrderType();
		$gatewayOrder->setInvoiceNumber($order->getId());
		$gatewayOrder->setDescription($order->getShortNumber());

		/*
		 * Set the customer's Bill To address
		 */
		// TODO: Implement attaching Billing Address info to transaction
//		$billingAddress = $order->getBillingAddress();
//		$customerAddress = new CustomerAddressType();
//		$customerAddress->setFirstName($billingAddress->firstName);
//		$customerAddress->setLastName($billingAddress->lastName);
//		$customerAddress->setCompany($billingAddress->businessName);
//		$customerAddress->setAddress($billingAddress->address1 . ($billingAddress->address2 ? ' ' . $billingAddress->address2 : ''));
//		$customerAddress->setCity($billingAddress->city);
//		$customerAddress->setState($billingAddress->getStateText());
//		$customerAddress->setZip($billingAddress->zipCode);
//		$customerAddress->setCountry($billingAddress->getCountryText());

		// Set the customer's identifying information
		$customerData = new CustomerDataType();
		$customerData->setType("individual");
		$customerData->setId($order->getCustomer()->id);
		$customerData->setEmail($order->getEmail());

		// Add values for transaction settings
		$duplicateWindowSetting = new SettingType();
		$duplicateWindowSetting->setSettingName("duplicateWindow");
		$duplicateWindowSetting->setSettingValue("60");

		// Set up some merchant-defined meta fields
		$customOrderIdField = (new UserFieldType())->setName('Craft Order ID')->setValue($order->id);

		// Create a TransactionRequestType object and add the previous objects to it
		$transactionRequestType = new TransactionRequestType();
		$transactionRequestType->setTransactionType("authCaptureTransaction");
		$transactionRequestType->setAmount($transaction->amount);
		$transactionRequestType->setOrder($gatewayOrder);
		$transactionRequestType->setPayment($paymentOne);
		// TODO: Implement attaching Billing Address info to transaction
//		$transactionRequestType->setBillTo($customerAddress);
		$transactionRequestType->setCustomer($customerData);
		$transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
		$transactionRequestType->addToUserFields($customOrderIdField);

		// Assemble the complete transaction request
		$request = new CreateTransactionRequest();
		$request->setMerchantAuthentication($merchantAuthentication);
		$request->setRefId($refId);
		$request->setTransactionRequest($transactionRequestType);

		// Create the controller and get the response
		$controller = new CreateTransactionController($request);
		$response = $controller->executeWithApiResponse($this->_environment);

		if ($response != null) {

			// Check to see if the API request was successfully received and acted upon
			if ($response->getMessages()->getResultCode() == "Ok") {

				// Since the API request was successful, look for a transaction response
				// and parse it to display the results of authorizing the card
				$transactionResponse = $response->getTransactionResponse();

				if ($transactionResponse != null && $transactionResponse->getMessages() != null) {

					$msg = "Successfully created transaction with Transaction ID: " . $transactionResponse->getTransId() . "\n"
						. " Transaction Response Code: " . $transactionResponse->getResponseCode() . "\n"
						. " Message Code: " . $transactionResponse->getMessages()[0]->getCode() . "\n"
						. " Auth Code: " . $transactionResponse->getAuthCode() . "\n"
						. " Description: " . $transactionResponse->getMessages()[0]->getDescription() . "\n";

					Craft::info($msg, 'acceptjs');

					return new RequestResponse(true, [
						'message' => $msg,
						'transactionReference' => $refId,
					]);

				} else {

					$msg = "Transaction Failed \n";
					if ($transactionResponse->getErrors() != null) {
						$msg .= " Error Code: " . $transactionResponse->getErrors()[0]->getErrorCode() . "\n";
						$msg .= " Error Message: " . $transactionResponse->getErrors()[0]->getErrorText() . "\n";
					}

					Craft::error($msg, 'acceptjs');

					return new RequestResponse(false, [
						'code' => 'payment.failed',
						'message' => $msg,
					]);

				}

			// Or, register errors if the API request wasn't successful
			} else {

				$msg = "Transaction Failed (API Request Unsuccessful) \n";
				$transactionResponse = $response->getTransactionResponse();

				if ($transactionResponse != null && $transactionResponse->getErrors() != null) {
					$msg .= " Error Code: " . $transactionResponse->getErrors()[0]->getErrorCode() . "\n";
					$msg .= " Error Message: " . $transactionResponse->getErrors()[0]->getErrorText() . "\n";
				} else {
					$msg .= " Error Code: " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
					$msg .= " Error Message: " . $response->getMessages()->getMessage()[0]->getText() . "\n";
				}

				Craft::error($msg, 'acceptjs');

				return new RequestResponse(false, [
					'code' => 'payment.failed',
					'message' => $msg,
				]);

			}

		} else {

			$msg = "No response returned.";

			Craft::error($msg, 'acceptjs');

			return new RequestResponse(false, [
				'code' => 'payment.failed',
				'message' => $msg,
			]);

		}

	}

	/**
	 * @inheritdoc
	 */
	public function processWebHook(): WebResponse
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function refund(Transaction $transaction): RequestResponseInterface
	{
		// TODO: Implement
		throw new NotImplementedException(Craft::t('commerce', 'This gateway does not support that functionality.'));
	}

	/**
	 * @inheritdoc
	 */
	public function supportsAuthorize(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsCapture(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsCompleteAuthorize(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsCompletePurchase(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsPaymentSources(): bool
	{
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsPurchase(): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsRefund(): bool
	{
		// TODO: Implement
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsPartialRefund(): bool
	{
		// TODO: Implement
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function supportsWebhooks(): bool
	{
		return false;
	}

}
