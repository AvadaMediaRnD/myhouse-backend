<?php

use yii\db\Migration;

/**
 * Class m190830_083001_add_1cid_for_service
 */
class m190830_083001_add_1cid_for_service extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('service', 'id_1c', $this->string(40)->notNull());
        $this->createIndex('fk_service_id1c_idx', 'service', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190830_083001_add_1cid_for_service cannot be reverted.\n";

        $this->dropIndex('fk_service_id1c_idx', 'service');
        $this->dropColumn('service', 'id_1c');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190830_083001_add_1cid_for_service cannot be reverted.\n";

        return false;
    }
    */
}
