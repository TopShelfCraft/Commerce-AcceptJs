<?php

namespace topshelfcraft\acceptjs\migrations;

use Craft;
use craft\db\Migration;


/**
 * @author    Top Shelf Craft (Michael Rog)
 * @package   Credits
 * @since     3.0.0
 */
class Install extends Migration
{

    /*
     * Public methods
     */

    /**
	 * @inheritdoc
     */
    public function safeUp()
    {

        if ($this->createTables())
        {
            $this->createIndexes();
            $this->addForeignKeys();
            Craft::$app->db->schema->refresh();
        }

        return true;

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {

        $this->removeTables();

        return true;

    }

    /*
     * Protected methods
     */

    /**
     * @return bool
     */
    protected function createTables()
    {
    	return false;
    }

    /**
     * @return void
     */
    protected function createIndexes()
    {
    }

    /**
     * @return void
     */
    protected function addForeignKeys()
    {
    }

    /**
     * @return void
     */
    protected function removeTables()
    {
    }

}
