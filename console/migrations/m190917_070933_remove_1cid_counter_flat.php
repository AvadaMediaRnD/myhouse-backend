<?php

use yii\db\Migration;

/**
 * Class m190917_070933_remove_1cid_counter_flat
 */
class m190917_070933_remove_1cid_counter_flat extends Migration {

    /**
     * {@inheritdoc}
     */
    public function up() {
        $this->dropIndex('fk_invoice_id1c_idx', 'invoice');
        $this->dropColumn('invoice', 'id_1c');

        $this->dropIndex('fk_counter_data_id1c_idx', 'counter_data');
        $this->dropColumn('counter_data', 'id_1c');

        $this->dropIndex('fk_flat_id1c_idx', 'flat');
        $this->dropColumn('flat', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function down() {
        echo "m190917_070933_remove_1cid_counter_flat cannot be reverted.\n";

        return false;
    }

}
