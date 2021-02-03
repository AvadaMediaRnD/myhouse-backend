<?php

use yii\db\Migration;

/**
 * Class m190829_192354_add_1cid_for_riser
 */
class m190829_192354_add_1cid_for_riser extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('riser', 'id_1c', $this->string(40)->notNull());

        $this->createIndex('fk_riser_id1c_idx', 'riser', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m190829_192354_add_1cid_for_riser cannot be reverted.\n";

        $this->dropIndex('fk_riser_id1c_idx', 'riser');
        $this->dropColumn('riser', 'id_1c');
    }

}
