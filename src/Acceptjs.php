<?php

namespace topshelfcraft\acceptjs;

use craft\base\Plugin;
use craft\commerce\services\Gateways;
use craft\events\RegisterComponentTypesEvent;
use craft\web\twig\variables\CraftVariable;
use topshelfcraft\acceptjs\commerce\Gateway;
use topshelfcraft\acceptjs\models\Settings;
use topshelfcraft\acceptjs\variables\AcceptjsVariable;
use yii\base\Event;


/**
 * @author    Top Shelf Craft (Michael Rog)
 * @package   acceptjs
 * @since     3.0.0
 */
class Acceptjs extends Plugin
{

	/*
	 * Static properties
	 */

    /**
     * @var Acceptjs
     */
    public static $plugin;

    /*
     * Public properties
     */

    /**
     * @var string
     */
    public $schemaVersion = '3.0.0.0';

    /*
     * Public methods
     */

    /**
	 *
     */
    public function init()
    {

        self::$plugin = $this;

		parent::init();

		Event::on(Gateways::class, Gateways::EVENT_REGISTER_GATEWAY_TYPES, function(RegisterComponentTypesEvent $e) {
			$e->types[] = Gateway::class;
		});

		Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $e) {
			/** @var CraftVariable $craftVariable */
			$craftVariable = $e->sender;
			$craftVariable->set('acceptjs', AcceptjsVariable::class);
		});

    }

	/**
	 * @inheritdoc
	 *
	 * @return Settings
	 */
    public function getSettings()
	{
		return parent::getSettings();
	}

	/*
     * Protected methods
     */

	/**
	 * @inheritdoc
	 */
	protected function createSettingsModel()
	{
		return new Settings();
	}

}
