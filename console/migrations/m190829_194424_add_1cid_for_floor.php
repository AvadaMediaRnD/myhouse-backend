<?php

use yii\db\Migration;

/**
 * Class m190829_194424_add_1cid_for_floor
 */
class m190829_194424_add_1cid_for_floor extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('floor', 'id_1c', $this->string(40)->notNull());

        $this->createIndex('fk_floor_id1c_idx', 'floor', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m190829_194424_add_1cid_for_floor cannot be reverted.\n";

        $this->dropIndex('fk_floor_id1c_idx', 'floor');
        $this->dropColumn('floor', 'id_1c');
    }

}
