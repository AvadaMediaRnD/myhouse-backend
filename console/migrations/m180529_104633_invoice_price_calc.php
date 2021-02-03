<?php

use yii\db\Migration;
use common\models\Invoice;
use common\models\InvoiceService;
use common\models\TariffService;

/**
 * Class m180529_104633_invoice_price_calc
 */
class m180529_104633_invoice_price_calc extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach (InvoiceService::find()->all() as $invoiceService) {
            $invoice = $invoiceService->invoice;
            $tariffService = TariffService::find()->where(['service_id' => $invoiceService->service_id, 'tariff_id' => $invoice->tariff_id])->one();
            $invoiceService->price_unit = $tariffService ? $tariffService->price_unit : 0;
            
            $invoiceService->save();
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180529_104633_invoice_price_calc nothing done. Returning success.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180529_104633_invoice_price_calc cannot be reverted.\n";

        return false;
    }
    */
}
