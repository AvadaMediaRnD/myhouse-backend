<?php

use yii\db\Migration;

/**
 * Class m190910_140003_add_user_id1c
 */
class m190910_140003_add_user_id1c extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('user', 'id_1c', $this->string(40)->null());
        $this->createIndex('fk_user_id1c_idx', 'user', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m190910_140003_add_user_id1c cannot be reverted.\n";

        $this->dropIndex('fk_user_id1c_idx', 'user');
        $this->dropColumn('user', 'id_1c');
    }

}
