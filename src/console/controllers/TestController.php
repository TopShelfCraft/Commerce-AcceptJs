<?php

namespace topshelfcraft\acceptjs\console\controllers;

use yii\console\Controller;
use yii\helpers\Console;


/**
 * TODO: Implement testing commands
 *
 * @author    Top Shelf Craft (Michael Rog)
 * @package   acceptjs
 * @since     3.0.0
 */
class TestController extends Controller
{


	/*
	 * Public properties
	 */

	/**
	 * @var string $defaultAction
	 */
	public $defaultAction = 'test';

	/**
	 * @var mixed
	 */
	public $foo;


	/*
	 * Public methods
	 */


	/**
	 * @inheritdoc
	 */
	public function options($actionId): array
	{
		$options = parent::options($actionId);
		$options[] = 'foo';
		return $options;
	}

	/**
	 * @throws \Exception
	 */
	public function actionTest()
	{
		Console::output("Test...");
	}


	/*
	 * Private methods
	 */


	/**
	 * Writes an error to console
	 * @param string $msg
	 */
	private function _writeErr($msg)
	{
		$this->stderr('Error', Console::BOLD, Console::FG_RED);
		$this->stderr(': ', Console::FG_RED);
		$this->stderr($msg . PHP_EOL);
	}


}
