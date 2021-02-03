<?php

use yii\db\Migration;

/**
 * Class m190829_201204_add_1cid_for_flat
 */
class m190829_201204_add_1cid_for_flat extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('flat', 'id_1c', $this->string(40)->notNull());
        $this->createIndex('fk_flat_id1c_idx', 'flat', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m190829_201204_add_1cid_for_flat cannot be reverted.\n";

        $this->dropIndex('fk_flat_id1c_idx', 'flat');
        $this->dropColumn('flat', 'id_1c');
    }

}
