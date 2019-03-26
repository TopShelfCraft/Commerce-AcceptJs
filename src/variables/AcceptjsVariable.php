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

}
