<?php

use yii\db\Migration;

/**
 * Class m190829_150522_add_1cid_for_house
 */
class m190829_150522_add_1cid_for_house extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('house', 'id_1c', $this->string(40)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190829_150522_add_1cid_for_house cannot be reverted.\n";

        $this->dropColumn('house', 'id_1c');
    }

}
