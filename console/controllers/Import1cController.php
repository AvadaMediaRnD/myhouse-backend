<?php

namespace console\controllers;

use Yii,
    yii\console\Controller,
    yii\helpers\Console;
use console\models\XmlParser1C;

/**
 * Import1cController
 */
class Import1cController extends Controller {

    /**
     * Parser invoices data from 1C
     * @return mixed
     */
    public function actionIndex() {
        $result = false;
        $folder = Yii::getAlias('@webroot') . Yii::$app->params['iport_invoice_dir_1c'];

        if (!file_exists($folder)) {
            mkdir($folder, 0766, true);
        }

        // List of files in folder
        $list = scandir($folder);

        foreach ($list as $item) {
            $file = $folder . '/' . $item;

            if ($item !== '.' && $item !== '..' && !is_dir($file)) {
                $result = $this->parceImportFile($file);

                if ($result === true) {
                    unlink($file);
                }
            }
        }

        if ($result === true) {
            $this->stdout('Import invoice data from 1C success!' . PHP_EOL, Console::FG_GREEN);
        } else {
            $this->stdout('Import invoice data from 1C error!' . PHP_EOL, Console::FG_RED);
        }
    }

    /**
     * Parser payment data from 1C
     * @return mixed
     */
    public function actionPayment() {
        $result = false;

        $folder = Yii::getAlias('@webroot') . Yii::$app->params['iport_pay_dir_1c'];

        if (!file_exists($folder)) {
            mkdir($folder, 0766, true);
        }

        // List of files in folder
        $list = scandir($folder);

        foreach ($list as $item) {
            $file = $folder . '/' . $item;

            if ($item !== '.' && $item !== '..' && !is_dir($file)) {
                $result = $this->parcePaymentFile($file);

                if ($result === true) {
                    unlink($file);
                }
            }
        }

        // Set invoice status
        XmlParser1C::changeInvoicesStatus();

        if ($result === true) {
            $this->stdout('Import payment data from 1C success!' . PHP_EOL, Console::FG_GREEN);
        } else {
            $this->stdout('Import payment data from 1C error!' . PHP_EOL, Console::FG_RED);
        }
    }

    /**
     * Trancate data from 1C
     * @return mixed
     */
    public function actionTrancate() {
        $result = XmlParser1C::trancateData();

        if ($result === true) {
            $this->stdout('Trancate invoice data from 1C success!' . PHP_EOL, Console::FG_GREEN);
        } else {
            $this->stdout('Trancate invoice data from 1C error!' . PHP_EOL, Console::FG_RED);
        }
    }

    /**
     * Parce import file of invoices
     * @param string $file
     * @return mixed
     */
    protected function parceImportFile(string $file): bool {
        $result = false;

        // Check mime type of file
        if (mime_content_type($file) === 'text/xml') {
            $result = XmlParser1C::parseData($file);
        }

        return $result;
    }

    /**
     * Parce import file of payment
     * @param string $file
     * @return mixed
     */
    protected function parcePaymentFile(string $file): bool {
        $result = false;

        // Check mime type of file
        if (mime_content_type($file) === 'text/xml') {
            $result = XmlParser1C::parsePaymentData($file);
        }

        return $result;
    }

}
