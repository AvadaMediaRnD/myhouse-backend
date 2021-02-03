<?php

use yii\db\Migration;

/**
 * Class m190831_133233_add_1cid_for_counter_data
 */
class m190831_133233_add_1cid_for_counter_data extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {
        $this->addColumn('counter_data', 'id_1c', $this->string(40)->notNull());
        $this->createIndex('fk_counter_data_id1c_idx', 'counter_data', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        echo "m190831_133233_add_1cid_for_counter_data cannot be reverted.\n";

        $this->dropIndex('fk_counter_data_id1c_idx', 'counter_data');
        $this->dropColumn('counter_data', 'id_1c');
    }

    /*
      // Use up()/down() to run migration code without a transaction.
      public function up()
      {

      }

      public function down()
      {
      echo "m190831_133233_add_1cid_for_counter_data cannot be reverted.\n";

      return false;
      }
     */
}
