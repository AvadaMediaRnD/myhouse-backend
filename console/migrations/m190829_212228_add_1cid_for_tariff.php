<?php

use yii\db\Migration;

/**
 * Class m190829_212228_add_1cid_for_tariff
 */
class m190829_212228_add_1cid_for_tariff extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('tariff', 'id_1c', $this->string(40)->notNull());
        $this->createIndex('fk_tariff_id1c_idx', 'tariff', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m190829_212228_add_1cid_for_tariff cannot be reverted.\n";

        $this->dropIndex('fk_tariff_id1c_idx', 'tariff');
        $this->dropColumn('tariff', 'id_1c');
    }

}
