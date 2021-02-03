<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\components\xlstemplate\Templator;
use common\components\xlstemplate\Settings;
use common\components\xlstemplate\LoopData;
use common\helpers\PriceHelper;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property string $uid
 * @property string $uid_date
 * @property string $period_start
 * @property string $period_end
 * @property int $status
 * @property int $is_checked
 * @property int $created_at
 * @property int $updated_at
 * @property int $flat_id
 * @property int $tariff_id
 * @property int $pay_company_id
 */
class Invoice extends \common\models\ZModel {

    const STATUS_PAID = 10;
    const STATUS_PAID_PART = 6;
    const STATUS_UNPAID = 5;
    const STATUS_PLANNED = 3; // будущая
    const STATUS_CHANGED = 2; // списана
    const STATUS_DISABLED = 0; // неактивна

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'invoice';
    }

    /**
     * @inheritdoc
     */
    public function fields() {
        $fields = [
            'id', 'uid', 'uid_date', 'period_start', 'period_end', 'status', 'is_checked',
            'created_at', 'updated_at', 'flat_id', 'tariff_id', 'pay_company_id',
        ];
        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            \yii\behaviors\TimestampBehavior::className(),
            [
                'class' => \common\components\ChangeLogBehavior::className(),
                'labelObject' => 'Квитанция',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['uid', 'status', 'flat_id'], 'required'],
            [['uid', 'uid_date', 'period_start', 'period_end'], 'string', 'max' => 255],
            [['status', 'is_checked', 'created_at', 'updated_at', 'flat_id', 'pay_company_id'], 'integer'],
            ['uid', 'unique'],
            [['flat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flat::className(), 'targetAttribute' => ['flat_id' => 'id']],
            [['tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tariff::className(), 'targetAttribute' => ['tariff_id' => 'id']],
            [['pay_company_id'], 'exist', 'skipOnError' => true, 'targetClass' => PayCompany::className(), 'targetAttribute' => ['pay_company_id' => 'id']],
            ['is_checked', 'default', 'value' => 1],
            ['flat_id', 'validateAccountStatus'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('model', 'ID'),
            'uid' => Yii::t('model', '№ квитанции'),
            'uid_date' => Yii::t('model', 'Дата'),
            'period_start' => Yii::t('model', 'Период с'),
            'period_end' => Yii::t('model', 'Период по'),
            'status' => Yii::t('model', 'Статус'),
            'is_checked' => Yii::t('model', 'Проведена'),
            'created_at' => Yii::t('model', 'Добавлен'),
            'updated_at' => Yii::t('model', 'Изменен'),
            'flat_id' => Yii::t('model', 'Квартира'),
            'tariff_id' => Yii::t('model', 'Тариф'),
            'pay_company_id' => Yii::t('model', 'Получатель'),
        ];
    }

    /**
     * 
     * @param string $attribute_name
     * @param array $params
     */
    public function validateAccountStatus($attribute_name, $params) {
        if (!empty($this->flat_id) && $this->flat && $this->flat->account && ($this->flat->account->status == Account::STATUS_DISABLED)
        ) {
            $this->addError($attribute_name, Yii::t('model', 'Лицевой счет этой квартиры неактивен.'));
            return false;
        }
        return true;
    }

    /**
     * Creates or updates transaction for this invoice price
     */
    public function makeTransactionForInvoice() {
        $account = Account::find()->where(['flat_id' => $this->flat_id])->one();
        if ($account) {
            $accountTransaction = $this->accountTransaction;
            if (!$accountTransaction) {
                $accountTransaction = new AccountTransaction();
                $accountTransaction->invoice_id = $this->id;
                $accountTransaction->generateUid();
                $accountTransaction->uid_date = date('Y-m-d', time());
                $accountTransaction->type = AccountTransaction::TYPE_OUT;
                $accountTransaction->currency_id = Currency::findDefault()->id;
            }
            $accountTransaction->transaction_purpose_id = 2; // invoice payment
            $accountTransaction->account_id = $account->id;
            $accountTransaction->amount = floatval($this->getPrice());
            $accountTransaction->status = $this->is_checked ? AccountTransaction::STATUS_COMPLETE : AccountTransaction::STATUS_WAITING;
            $accountTransaction->save();
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlat() {
        return $this->hasOne(Flat::className(), ['id' => 'flat_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceServices() {
        return $this->hasMany(InvoiceService::className(), ['invoice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices() {
        return $this->hasMany(Service::className(), ['id' => 'service_id'])
                        ->via('invoiceServices');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments() {
        return $this->hasMany(Payment::className(), ['invoice_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariff() {
        return $this->hasOne(Tariff::className(), ['id' => 'tariff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayCompany() {
        return $this->hasOne(PayCompany::className(), ['id' => 'pay_company_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountTransaction() {
        return $this->hasOne(AccountTransaction::className(), ['invoice_id' => 'id']);
    }

    /**
     * 
     * @return float
     */
    public function getPrice() {
        return $this->getInvoiceServices()->sum('price');

//        $invoiceServiceAmounts = ArrayHelper::map($this->getInvoiceServices()->asArray()->all(), 'service_id', 'amount');
//        $tariffServicePrices = ArrayHelper::map($this->tariff->getTariffServices()->asArray()->all(), 'service_id', 'price_unit');
//        $sum = 0;
//        foreach ($invoiceServiceAmounts as $k => $invoiceServiceAmount) {
//            if (isset($tariffServicePrices[$k])) {
//                $sum += floatval($invoiceServiceAmount) * floatval($tariffServicePrices[$k]);
//            }
//        }
//        return $sum;
    }

    /**
     * @return array
     */
    public static function getStatusOptions() {
        return [
            static::STATUS_PAID => Yii::t('model', 'Оплачена'),
            static::STATUS_PAID_PART => Yii::t('model', 'Частично оплачена'),
            static::STATUS_UNPAID => Yii::t('model', 'Неоплачена'),
//            static::STATUS_PLANNED => Yii::t('model', 'Будущая'),
//            static::STATUS_CHANGED => Yii::t('model', 'Списана'),
//            static::STATUS_DISABLED => Yii::t('model', 'Неактивна'),
        ];
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabel($status = null) {
        $status = $status == null ? $this->status : $status;
        $options = static::getStatusOptions();
        return isset($options[$status]) ? $options[$status] : null;
    }

    /**
     * @param null $status
     * @return mixed|null
     */
    public function getStatusLabelHtml($status = null) {
        $status = $status == null ? $this->status : $status;
        $statusLabel = $this->getStatusLabel($status);
        $itemClass = 'label-default';
        if ($status == static::STATUS_PAID) {
            $itemClass = 'label-success';
        } elseif ($status == static::STATUS_PAID_PART) {
            $itemClass = 'label-warning';
        } elseif ($status == static::STATUS_UNPAID) {
            $itemClass = 'label-danger';
        } elseif ($status == static::STATUS_DISABLED) {
            $itemClass = 'label-warning';
        } elseif ($status == static::STATUS_PLANNED) {
            $itemClass = 'label-info';
        } elseif ($status == static::STATUS_CHANGED) {
            $itemClass = 'label-primary';
        }
        return '<small class="label ' . $itemClass . '">' . $statusLabel . '</small>';
    }

    /**
     * @return array
     */
    public function getInvoiceServiceOptions() {
        return ArrayHelper::map($this->invoiceServices, 'id', function ($model) {
                    return $model->service->name . ', сумма: ' . PriceHelper::format($model->price);
                });
    }

    /**
     * @param null $is_checked
     * @return mixed|null
     */
    public function getIsCheckedLabel($is_checked = null) {
        $is_checked = $is_checked == null ? $this->is_checked : $is_checked;
        return $is_checked ? 'Проведена' : 'Не проведена';
    }

    /**
     * @param null $is_checked
     * @return mixed|null
     */
    public function getIsCheckedLabelHtml($is_checked = null) {
        $is_checked = $is_checked == null ? $this->is_checked : $is_checked;
        $isCheckedLabel = $this->getIsCheckedLabel($is_checked);
        $itemClass = $is_checked ? 'label-success' : 'label-danger';
        return '<small class="label ' . $itemClass . '">' . $isCheckedLabel . '</small>';
    }

    /**
     * get if user read this invoice
     * @param null $userId
     * @return bool
     */
    public function getIsUserView($userId = null) {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }
        return UserInvoiceView::find()->where(['user_id' => $userId, 'invoice_id' => $this->id])->exists();
    }

    /**
     * set user read this invoice
     * @param null $userId
     */
    public function setIsUserView($userId = null) {
        if (!$userId) {
            $userId = Yii::$app->user->id;
        }
        if (!$this->getIsUserView($userId)) {
            Yii::$app->db->createCommand()->insert(UserInvoiceView::tableName(), ['user_id' => $userId, 'invoice_id' => $this->id])->execute();
        }
    }

    public function getUidDate() {
        if (!$this->uid_date) {
            return $this->uid_date;
        }
        return date(Yii::$app->params['dateFormat'], strtotime($this->uid_date));
    }

    public function getPeriodStart() {
        if (!$this->period_start) {
            return $this->period_start;
        }
        return date(Yii::$app->params['dateFormat'], strtotime($this->period_start));
    }

    public function getPeriodEnd() {
        if (!$this->period_end) {
            return $this->period_end;
        }
        return date(Yii::$app->params['dateFormat'], strtotime($this->period_end));
    }

    /**
     * Get month + year for invoice print
     * @param boolean $ua is months in ua/ru
     * @return string
     */
    public function getMonthYearPrint($ua = false) {
        return \common\helpers\DateHelper::getMonthYearLabel($this->period_end, $ua);
    }

    public function getPeriodPrint() {
        return $this->getPeriodStart() . ' - ' . $this->getPeriodEnd();
    }

    /**
     * Get flat address for invoice print
     * @return string
     */
    public function getAddressPrint() {
        $userNameShort = $this->flat->user ? $this->flat->user->getFullname(true) : '';
        $address = $this->flat->house->address;
        return ($userNameShort . ' ' . $address . ' квартира ' . $this->flat->flat);
    }

    /**
     * Create file with template
     * @param InvoiceTemplate $template
     * @param string $type
     */
    public function getTemplateFile($template, $type) {
        if ($template) {
            $ext = ($type == 'pdf' ? 'pdf' : 'xls');
            $writer = ($type == 'pdf' ? 'Pdf' : 'Xls');

            $dirSub = '/upload/Invoice/' . $this->id;
            $dir = Yii::getAlias('@frontend/web' . $dirSub);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $invoiceName = str_replace([' ', '/', "\\", ''], '', ($this->uid . '_' . $this->getUidDate()));
            $pathNoExt = $dirSub . '/invoice-' . $invoiceName . '.';
            $path = $this->fillTemplate($template->file, $pathNoExt . 'xls');

            if ($ext == 'xls') {
                return $path;
            }

            $class = \PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf::class;
            \PhpOffice\PhpSpreadsheet\IOFactory::registerWriter('Pdf', $class);

            //Load temp file
            $phpXls = \PhpOffice\PhpSpreadsheet\IOFactory::load(Yii::getAlias('@frontend/web') . $path);

            $xmlWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($phpXls, $writer);
            // $xmlWriter = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf($phpXls);
            $path = $pathNoExt . $ext;
            $xmlWriter->save(Yii::getAlias('@frontend/web') . $path);

            return $path;
        }
        return false;
    }

    /**
     * Send file to owner email
     * @param string $file
     */
    public function sendTemplateFileToEmail($file) {
        if (!file_exists(Yii::getAlias('@frontend/web') . $file)) {
            return false;
        }

        $email = $this->flat->user->email;

        if ($email) {
            $title = 'Квитанция на оплату #' . $this->uid . ' от ' . $this->getUidDate();
            $message = 'Вам отправлена квитанция #' . $this->uid . ' от ' . $this->getUidDate()
                    . "\r\n";

            // send email
            \Yii::$app->mailer->compose()
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setTo($email)
                    ->setSubject($title)
                    ->setTextBody(strip_tags($message))
                    ->setHtmlBody(nl2br($message))
                    ->attach(Yii::getAlias('@frontend/web') . $file)
                    ->send();
        }
    }

    /**
     * 
     * @param string $path
     * @return string
     */
    protected function fillTemplate($path, $outpath) {
        $outputDir = dirname(Yii::getAlias('@frontend/web') . $outpath);
        $fileName = '/' . pathinfo($outpath)['basename'];

        $templator = new Templator(Yii::getAlias('@frontend/web') . $path, $outputDir, $fileName);

        $total = floatval($this->getPrice());
        $accountBalance = floatval($this->flat->account ? $this->flat->account->getBalance() : 0);
        $totalDebt = $accountBalance > 0 ? 0 : ($total - ($accountBalance + $total));
        $payCompanyModel = PayCompany::find()->one();
        $payCompany = $payCompanyModel ? $payCompanyModel->description : '';

        $settingsData = [
            'invoiceNumber' => (string) $this->uid,
            'invoiceDate' => (string) $this->getUidDate(),
            'invoiceDateNumberText' => '№ ' . (string) $this->uid . ' від ' . (string) $this->getUidDate(),
            'userName' => (string) $this->flat->user->fullname,
            'userNameShort' => (string) $this->flat->user->getFullname(true),
            'userPhone' => (string) $this->flat->user->profile->phone,
            'flat' => (string) $this->flat->flat,
            'address' => (string) $this->flat->house->address,
            'total' => $total,
            'totalNds' => PriceHelper::format($total * 0.166667, true, false, '', ''),
            'totalNoNds' => PriceHelper::format($total - ($total * 0.166667), true, false, '', ''),
            'totalText' => PriceHelper::text($total),
            'totalNdsText' => PriceHelper::text($total * 0.166667),
            'totalNoNdsText' => PriceHelper::text($total - ($total * 0.166667)),
            'legacyTextTop' => 'Ми, що нижче підписалися, представник Замовника ' . $this->flat->user->getFullname(true) . ', з одного боку, і представник Виконавця ТОВАРИСТВО З ОБМЕЖЕНОЮ ВІДПОВІДАЛЬНІСТЮ «ТЕРРАКС» ЛТД директор Медведєв Андрій Валерійович, з іншого боку, склали цей акт про те, що на підставі наведених документів:',
            'legacyTextBottom' => 'Загальна вартість робіт (послуг) склала без ПДВ ' . PriceHelper::text($total - ($total * 0.166667)) . ', ПДВ ' . PriceHelper::text($total * 0.166667) . ', загальна вартість робіт (послуг) із ПДВ ' . PriceHelper::text($total) . '.',
            'totalDebt' => floatval($totalDebt),
            'payCompany' => (string) $payCompany,
            'invoiceMonth' => (string) $this->getMonthYearPrint(true),
            'invoicePeriod' => (string) $this->getPeriodPrint(),
            'accountNumber' => (string) $this->flat->account->uid,
            'accountBalance' => floatval($accountBalance),
            'userNameShort' => (string) ($this->flat->user ? $this->flat->user->getFullname(true) : ''),
            'invoiceAddress' => (string) $this->getAddressPrint(),
        ];
        $settings = new Settings($settingsData);
        $map = [
            'serviceIndex',
            'serviceName',
            'counterAmountLast',
            'counterAmount',
            'serviceAmount',
            'serviceUnit',
            'servicePrice',
            'serviceTotal',
            'servicePriceUnit',
        ];
        $source = [];
        foreach ($this->getInvoiceServices()->all() as $k => $invoiceService) {
            $source[] = [
                $k + 1,
                (string) $invoiceService->service->name,
                ($invoiceService->counterData && $invoiceService->counterData->counterDataLast) ? $invoiceService->counterData->counterDataLast->amount_total : '',
                $invoiceService->counterData ? $invoiceService->counterData->amount_total : '',
                floatval($invoiceService->amount) . ' ',
                (string) $invoiceService->service->serviceUnit->name,
                number_format($invoiceService->price_unit, 2) . ' ',
                number_format($invoiceService->price, 2) . ' ',
                (string) $invoiceService->price_unit . '/' . $invoiceService->service->serviceUnit->name,
            ];
        }

        try {

            $loopData = new LoopData();
            $loopData->setMap($map);
            $loopData->setSource($source);
            $settings->addLoop(1, $loopData);
            $templator->render($settings);
            $templator->save();
        } catch (\PHPExcel_Exception $e) {
            if ($e->getMessage() != 'No cells exist within the specified range') {
                throw $e;
            }
        }

        return $outpath;
    }

}
