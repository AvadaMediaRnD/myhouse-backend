<?php

use yii\db\Migration;

/**
 * Class m190830_084729_add_1cid_for_service_unit
 */
class m190830_084729_add_1cid_for_service_unit extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('service_unit', 'id_1c', $this->string(40)->notNull());
        $this->createIndex('fk_service_unit_id1c_idx', 'service_unit', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m190830_084729_add_1cid_for_service_unit cannot be reverted.\n";

        $this->dropIndex('fk_service_unit_id1c_idx', 'service_unit');
        $this->dropColumn('service_unit', 'id_1c');
    }

}
