<?php

use yii\db\Migration;

/**
 * Class m191029_185554_add_user_to_account_transaction
 */
class m191029_185554_add_user_to_account_transaction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account_transaction', 'user_id', $this->integer(11)->null());
        $this->createIndex('fk_account_transaction_user_idx', 'account_transaction', 'user_id');
        $this->addForeignKey('fk_account_transaction_user', 'account_transaction', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191029_185554_add_user_to_account_transaction cannot be reverted.\n";

        $this->dropForeignKey('fk_account_transaction_user', 'account_transaction');
        $this->dropIndex('fk_account_transaction_user_idx', 'account_transaction');
        $this->dropColumn('account_transaction', 'user_id');
    }
}
