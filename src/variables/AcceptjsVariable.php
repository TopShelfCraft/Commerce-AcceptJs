<?php

namespace topshelfcraft\acceptjs\variables;

use topshelfcraft\acceptjs\Acceptjs;


/**
 * @author    Top Shelf Craft (Michael Rog)
 * @package   acceptjs
 * @since     3.0.0
 */
class AcceptjsVariable
{

	/**
	 * @return string
	 */
	public function getClientKey()
	{
		return Acceptjs::$plugin->getSettings()->clientKey;
	}

	/**
	 * @return string
	 */
	public function getApiLoginId()
	{
		return Acceptjs::$plugin->getSettings()->apiLoginId;
	}

	/**
	 * @return string
	 */
	public function getEnvironment()
	{
		return Acceptjs::$plugin->getSettings()->environment;
	}

	/**
	 * @return string
	 */
	public function getScriptUrl()
	{
		return $this->getEnvironment() == 'production' ? 'https://js.authorize.net/v1/Accept.js' : 'https://jstest.authorize.net/v1/Accept.js';
	}

	/**
	 * @return string
	 */
	public function getScriptHtml()
	{
		return '<script type="text/javascript" src="'  . $this->getScriptUrl() . '" charset="utf-8"></script>';
	}

}
