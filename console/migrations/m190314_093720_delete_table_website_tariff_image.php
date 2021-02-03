<?php

use yii\db\Migration;

/**
 * Class m190314_093720_delete_table_website_tariff_image
 */
class m190314_093720_delete_table_website_tariff_image extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TABLE IF EXISTS `website_tariff_image`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190314_093720_delete_table_website_tariff_image nothing done. Returning success.\n";
        
        return true;
    }

}
