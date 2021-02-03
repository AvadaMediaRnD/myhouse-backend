<?php

use yii\db\Migration;

/**
 * Class m190830_101521_add_1cid_for_invoice
 */
class m190830_101521_add_1cid_for_invoice extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('invoice', 'id_1c', $this->string(40)->notNull());
        $this->createIndex('fk_invoice_id1c_idx', 'invoice', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m190830_101521_add_1cid_for_invoice cannot be reverted.\n";

        $this->dropIndex('fk_invoice_id1c_idx', 'invoice');
        $this->dropColumn('invoice', 'id_1c');
    }

}
