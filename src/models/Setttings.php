<?php

namespace topshelfcraft\acceptjs\models;

use craft\base\Model;


/**
 * @author    Top Shelf Craft (Michael Rog)
 * @package   acceptjs
 * @since     3.0.0
 */
class Settings extends Model
{

	/*
	 * Public Properties
	 */

	public $apiLoginId = '';
	public $transactionKey = '';
	public $clientKey = '';
	public $environment = 'PRODUCTION';

}
