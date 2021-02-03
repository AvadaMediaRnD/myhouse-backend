<?php

use yii\db\Migration;

/**
 * Class m190917_202046_remove_1cid_user
 */
class m190917_202046_remove_1cid_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->dropIndex('fk_user_id1c_idx', 'user');
        $this->dropColumn('user', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m190917_202046_remove_1cid_user cannot be reverted.\n";

        return false;
    }
}
