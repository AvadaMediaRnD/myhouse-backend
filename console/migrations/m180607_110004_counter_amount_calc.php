<?php

use yii\db\Migration;
use common\models\CounterData;

/**
 * Class m180607_110004_counter_amount_calc
 */
class m180607_110004_counter_amount_calc extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach (CounterData::find()->orderBy(['id' => SORT_ASC])->all() as $counterData) {
            // $counterData->amount = CounterData::getAmountByData($counterData->amount_total, $counterData->uid_date, $counterData->service_id, $counterData->flat_id);
            $counterData->save();
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180607_110004_counter_amount_calc nothing done. Returning success.\n";
        
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180607_110004_counter_amount_calc cannot be reverted.\n";

        return false;
    }
    */
}
