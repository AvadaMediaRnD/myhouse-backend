<?php

namespace console\models;

use Yii,
    yii\helpers\ArrayHelper,
    yii\base\Model;
use XMLReader,
    SimpleXMLElement;
use common\models\House,
    common\models\Section,
    common\models\Riser,
    common\models\Floor,
    common\models\Flat,
    common\models\Account,
    common\models\ServiceUnit,
    common\models\Service,
    common\models\Invoice,
    common\models\CounterData,
    common\models\AccountTransaction,
    common\models\User,
    common\models\Profile,
    common\models\UserNote;

/**
 * Description of XmlParser1C
 * Parse import files from 1C and add to DB
 * @author fobos
 */
class XmlParser1C extends Model {

    // parce invoice file
    public static function parseData(string $file): bool {
        $result = false;

        $reader = new XMLReader();
        $reader->open($file);

        while ($reader->read()) {
            $elementName = $reader->name;

            if ($elementName === 'Квитанция') {
                $result = self::setReceiptInfo($reader->readOuterXML());
            } elseif ($elementName === 'Оплата') {
                $result = self::setReceiptInfo($reader->readOuterXML());
            }
        }

        $reader->close();

        return $result;
    }

    // parce payment file
    public static function parsePaymentData(string $file): bool {
        $result = false;

        $reader = new XMLReader();
        $reader->open($file);

        while ($reader->read()) {
            $elementName = $reader->name;

            if ($elementName === 'Оплата') {
                $result = self::setPaymentInfo($reader->readOuterXML());
            }
        }

        $reader->close();

        return $result;
    }

    // parce file
    public static function trancateData(): bool {
        $result = true;

        Yii::$app->db->createCommand('set foreign_key_checks=0')->execute();
        Yii::$app->db->createCommand()->truncateTable('account_transaction')->execute();
        Yii::$app->db->createCommand()->truncateTable('account')->execute();
        Yii::$app->db->createCommand()->truncateTable('invoice')->execute();
        Yii::$app->db->createCommand()->truncateTable('counter_data')->execute();
        Yii::$app->db->createCommand()->truncateTable('service_unit')->execute();
        Yii::$app->db->createCommand()->truncateTable('service')->execute();
        Yii::$app->db->createCommand()->truncateTable('flat')->execute();
        Yii::$app->db->createCommand()->truncateTable('floor')->execute();
        Yii::$app->db->createCommand()->truncateTable('riser')->execute();
        Yii::$app->db->createCommand()->truncateTable('section')->execute();
        Yii::$app->db->createCommand()->truncateTable('house')->execute();
        Yii::$app->db->createCommand()->truncateTable('invoice_service')->execute();
        Yii::$app->db->createCommand('set foreign_key_checks=1')->execute();

        return $result;
    }

    // Set one Receipt Invoice Data
    private static function setReceiptInfo(string $objData): bool {
        $result = false;

        $node = new SimpleXMLElement($objData);

        if (isset($node->ref)) {

            if ($node->Дом) {
                $houseId = self::setHouseInfo($node->Дом);
            }
            if ($node->Этаж && isset($houseId)) {
                $floorId = self::setFloorInfo($node->Этаж, $houseId);
            }
            if ($node->Секция && isset($houseId)) {
                $sectionId = self::setSectionInfo($node->Секция, $houseId);
            }
            if ($node->Стояк && isset($houseId)) {
                $riserId = self::setRiserInfo($node->Стояк, $houseId);
            }
            if ($node->ВладелецПомещения) {
                $userId = self::setUserInfo($node->ВладелецПомещения);
            }
            if ($node->УслугиКвитанции) {
                $serviceUnitId = self::setServiceUnitInfo($node->Услуги);
                $serviceId = self::setServiceInfo($node->Услуги, $serviceUnitId);
            }
            if ($node->Помещение) {
                $flatId = self::setFlatInfo($node->Помещение, $houseId, $userId, $sectionId, $riserId, $floorId);
            }
            if (isset($serviceUnitId) && $serviceUnitId > 0 && $node->ПоказаниеСчетчика) {
                self::setCounterDataInfo($node->ПоказаниеСчетчика, $serviceId, $flatId);
            }
            if (isset($flatId) && $flatId > 0) {
                $invoceId = self::setInvoiceInfo($node, $flatId);
            }
            if (isset($invoceId) && $invoceId > 0 && isset($flatId) && $flatId > 0) {
                $accountId = self::getAccountId($node->Помещение, $flatId);
                $result = self::setAccountTransactionInfo($node, $invoceId, $accountId);
            }
        } else {
            $result = true;
        }

        unset($node);

        return $result;
    }

    // Set one Receipt Payment Data
    private static function setPaymentInfo(string $objData): bool {
        $result = true;

        $node = new SimpleXMLElement($objData);

        if (isset($node->ref)) {
            $result = self::setPayAccountTransactionInfo($node);
        }

        unset($node);

        return $result;
    }

    // Get data for House and add to DB
    private static function setHouseInfo(object $node): int {
        $result = 0;

        if (isset($node->Ref)) {

            $name = $node->Наименование ?? null;
            $address = $node->Адрес ?? null;
            $numer = $node->Номер ?? null;
            $full_address = $address . ', ' . $numer;
            $create_date = $update_date = time();

            if (($model = House::findOne(['id_1c' => $node->Ref])) === null) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model = new House();
                    $model->name = (string) $name;
                    $model->address = (string) $full_address;
                    $model->id_1c = (string) $node->Ref;
                    $model->created_at = $create_date;
                    $model->updated_at = $update_date;
                    $model->save();
                    $transaction->commit();
                } catch (Exception $ex) {
                    $transaction->rollback();
                }
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->name = (string) $name;
                    $model->address = (string) $full_address;
                    $model->updated_at = $update_date;
                    $model->save();
                    $transaction->commit();
                } catch (Exception $ex) {
                    $transaction->rollback();
                }
            }

            $result = $model->id;
        }

        return $result;
    }

    // Get data for Section and add to DB
    private static function setSectionInfo(object $node, int $houseId): int {
        $result = 0;

        if (isset($node->Секция) && isset($node->Секция->ref)) {

            if ($houseId != 0):

                $name = $node->Секция->Наименование ?? null;

                if (($model = Section::findOne(['id_1c' => $node->Секция->ref])) === null) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model = new Section();
                        $model->name = (string) $name;
                        $model->sort = 0;
                        $model->id_1c = (string) $node->Секция->ref;
                        $model->house_id = $houseId;
                        $model->save();
                        $transaction->commit();
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                } else {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model->name = (string) $name;
                        $model->save();
                        $transaction->commit();
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                }

                $result = $model->id;

            endif;
        }

        return $result;
    }

    // Get data for Riser and add to DB
    private static function setRiserInfo(object $node, int $houseId): int {
        $result = 0;

        if (isset($node->Стояк) && isset($node->Стояк->ref)) {

            if ($houseId != 0):

                $name = $node->Стояк->Наименование ?? null;

                if (($model = Riser::findOne(['id_1c' => $node->Стояк->ref])) === null) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model = new Riser();
                        $model->name = (string) $name;
                        $model->sort = 0;
                        $model->id_1c = (string) $node->Стояк->ref;
                        $model->house_id = $houseId;
                        $model->save();
                        $transaction->commit();
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                } else {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model->name = (string) $name;
                        $model->save();
                        $transaction->commit();
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                }

                $result = $model->id;
            endif;
        }

        return $result;
    }

    // Get data for Floor and add to DB
    private static function setFloorInfo(object $node, int $houseId): int {
        $result = 0;

        if (isset($node->Этаж) && isset($node->Этаж->ref)) {

            if ($houseId != 0):

                $name = $node->Этаж->Наименование ?? null;

                if (($model = Floor::findOne(['id_1c' => $node->Этаж->ref])) === null) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model = new Floor();
                        $model->name = (string) $name;
                        $model->sort = 0;
                        $model->id_1c = (string) $node->Этаж->ref;
                        $model->house_id = $houseId;
                        $model->save();
                        $transaction->commit();
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                } else {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model->name = (string) $name;
                        $model->save();
                        $transaction->commit();
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                }

                $result = $model->id;
            endif;
        }

        return $result;
    }

    // Get data for User and add to DB
    private static function setUserInfo(object $node): int {
        $result = 0;

        if (isset($node->ref)) {

            $name = $node->Наименование ?? '';
            $number = $node->Номер ?? '';
            $inn = $node->ИНН ?? '';
            $email = $node->Email ?? self::getEmail(12);
            $firstName = $node->Имя ?? null;
            $lastName = $node->Фамилия ?? null;
            $middleName = $node->Отчество ?? null;
            $phone = $node->МобильныйНомер ?? null;
            $viber = $node->Viber ?? null;
            $telegram = $node->Telegranm ?? null;
            $created_at = (isset($node->ДатаСоздания)) ? strtotime((string) $node->ДатаСоздания) : time();
            $updated_at = (isset($node->ДатаРедактирования)) ? strtotime((string) $node->ДатаРедактирования) : time();

            $descr = "Наименование: $name, Номер: $number, ИНН: $inn";

            if (($model = User::findOne(['uid' => (string) $node->ref])) === null) {

                if (($modelUser = User::findOne(['email' => $email])) === null):

                    $password = User::generatePassword(8);
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model = new User();
                        $model->email = (string) $email;
                        $model->uid = (string) $node->ref;
                        $model->status = User::STATUS_NEW;
                        $model->setPassword($password);
                        $model->generateAuthKey();
                        $model->created_at = $created_at;
                        $model->updated_at = $updated_at;
                        $model->save();

                        $userId = $model->id;

                        $model_profile = new Profile();
                        $model_profile->user_id = $userId;
                        $model_profile->firstname = (string) $firstName;
                        $model_profile->lastname = (string) $lastName;
                        $model_profile->middlename = (string) $middleName;
                        $model_profile->phone = (string) $phone;
                        $model_profile->viber = (string) $viber;
                        $model_profile->telegram = (string) $telegram;
                        $model_profile->save();

                        $model_note = new UserNote();
                        $model_note->user_id = $userId;
                        $model_note->description = $descr;
                        $model_note->save();

                        $transaction->commit();

                        // Send invite message for new user if his has email
                        if (isset($node->Email) && !empty((string) $node->Email)) {
                            self::sendInviteMail((string) $email, $password, (string) $node->ref);
                        }

                        $result = $userId;
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }

                else:
                    $modelUser->uid = (string) $node->ref;
                    $modelUser->save();
                    $userId = $modelUser->id;

                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model_profile = Profile::findOne(['user_id' => $userId]);
                        $model_profile->firstname = (string) $firstName;
                        $model_profile->lastname = (string) $lastName;
                        $model_profile->middlename = (string) $middleName;
                        $model_profile->phone = (string) $phone;
                        $model_profile->viber = (string) $viber;
                        $model_profile->telegram = (string) $telegram;
                        $model_profile->save();

                        $model_note = UserNote::findOne(['user_id' => $userId]);
                        $model_note->description = $descr;
                        $model_note->save();

                        $transaction->commit();
                        $result = $userId;
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                endif;
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $userId = $model->id;

                    $model_profile = Profile::findOne(['user_id' => $userId]);
                    $model_profile->firstname = (string) $firstName;
                    $model_profile->lastname = (string) $lastName;
                    $model_profile->middlename = (string) $middleName;
                    $model_profile->phone = (string) $phone;
                    $model_profile->viber = (string) $viber;
                    $model_profile->telegram = (string) $telegram;
                    $model_profile->save();

                    $model_note = UserNote::findOne(['user_id' => $userId]);
                    $model_note->description = $descr;
                    $model_note->save();

                    $transaction->commit();
                    $result = $userId;
                } catch (Exception $ex) {
                    $transaction->rollback();
                }
            }
        }

        return $result;
    }

    // Get data for Tariff and add to DB
    /* Temporary not used
      private static function setTariffInfo(object $node): int {
      $result = 0;

      if (isset($node->ref)) {

      $name = $node->Наименование . ' #' . $node->Code ?? null;
      $description = $node->Описание ?? null;
      $isDefault = (isset($node->ТарифПоУмолчанию) && $node->ТарифПоУмолчанию === 'true') ? 1 : 0;
      $create_date = $update_date = time();

      if (($model = Tariff::findOne(['id_1c' => $node->Ref])) === null) {
      $transaction = Yii::$app->db->beginTransaction();
      try {
      $model = new Tariff();
      $model->name = (string) $name;
      $model->description = (string) $description;
      $model->id_1c = (string) $node->ref;
      $model->is_default = $isDefault;
      $model->created_at = $create_date;
      $model->updated_at = $update_date;
      $model->save();
      $transaction->commit();
      } catch (Exception $ex) {
      $transaction->rollback();
      }
      } else {
      $transaction = Yii::$app->db->beginTransaction();
      try {
      $model->name = (string) $name;
      $model->description = (string) $description;
      $model->is_default = $isDefault;
      $model->updated_at = $update_date;
      $model->save();
      $transaction->commit();
      } catch (Exception $ex) {
      $transaction->rollback();
      }
      }

      $result = $model->id;
      }

      return $result;
      }
     * 
     */

    // Get data for Tariff Service add to DB
    /* Temporary not used
      private static function setTariffServiceInfo(object $node, int $tarifId, int $serviceId): bool {
      $result = false;

      if (isset($node->ref)) {

      $price = $node->УслугиТарифа->Услуга->ЦенаЗаУслуги ?? 0.00;

      if (($model = TariffService::findOne(['tariff_id' => $tarifId, 'service_id' => $serviceId])) === null) {
      $transaction = Yii::$app->db->beginTransaction();
      try {
      $model = new TariffService();
      $model->price_unit = floatval($price);
      $model->tariff_id = $tarifId;
      $model->service_id = $serviceId;
      $model->save();
      $transaction->commit();
      $result = true;
      } catch (Exception $ex) {
      $transaction->rollback();
      }
      } else {
      $transaction = Yii::$app->db->beginTransaction();
      try {
      $model->price_unit = floatval($price);
      $model->save();
      $transaction->commit();
      $result = true;
      } catch (Exception $ex) {
      $transaction->rollback();
      }
      }
      }

      return $result;
      }
     * 
     */

    // Get data for Flat and add to DB
    private static function setFlatInfo(object $node, int $houseId, $userId = null, $sectionId = null, $riserId = null, $floorId = null): int {
        $result = 0;

        if (isset($node->Ref)) {

            if ($houseId !== null || $houseId != 0):

                $flat = $node->НомерПомещения ?? 0;
                $square = (isset($node->Площадь)) ? (float) $node->Площадь : null;
                $create_date = (isset($node->ДатаСоздания)) ? strtotime((string) $node->ДатаСоздания) : time();
                $update_date = (isset($node->ДатаРедактирования)) ? strtotime((string) $node->ДатаРедактирования) : time();

                if (($model = Account::findOne(['uid' => $node->Ref])) === null) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $modelFlat = new Flat();
                        $modelFlat->flat = (int) $flat;
                        $modelFlat->square = $square;
                        $modelFlat->house_id = $houseId;
                        $modelFlat->user_id = $userId;
                        $modelFlat->created_at = $create_date;
                        $modelFlat->updated_at = $update_date;
                        $modelFlat->section_id = $sectionId;
                        $modelFlat->riser_id = $riserId;
                        $modelFlat->floor_id = $floorId;
                        $modelFlat->tariff_id = null;
                        $modelFlat->save();
                        $transaction->commit();
                        $flatId = $modelFlat->id;

                        $result = $flatId;

                        $transactionSecond = Yii::$app->db->beginTransaction();
                        try {
                            $modelAccount = new Account();
                            $modelAccount->flat_id = $flatId;
                            $modelAccount->uid = (string) $node->Ref;
                            $modelAccount->status = Account::STATUS_ACTIVE;
                            $modelAccount->save();
                            $transactionSecond->commit();
                        } catch (Exception $ex) {
                            $transactioSecond->rollback();
                        }
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                } else {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $modelFlat = Flat::findOne(['id' => $model->flat_id]);
                        $modelFlat->flat = (int) $flat;
                        $modelFlat->square = $square ?? $model->square;
                        $modelFlat->user_id = $userId;
                        $modelFlat->updated_at = $update_date;
                        $modelFlat->section_id = $sectionId ?? $model->section_id;
                        $modelFlat->riser_id = $riserId ?? $model->riser_id;
                        $modelFlat->floor_id = $floorId ?? $model->floor_id;
                        $modelFlat->save();
                        $transaction->commit();

                        $result = $model->flat_id;
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                }

            endif;
        }

        unset($node);

        return $result;
    }

    // Get data for Service Unit and add to DB
    private static function setServiceUnitInfo(object $node): int {
        $result = 0;

        if (isset($node->Услуга->ref) && (string) $node->Услуга->УслугаПоСчетчику === 'true') {

            $name = $node->Услуга->ЕдиницаИзмирения ?? null;

            if (($model = ServiceUnit::findOne(['id_1c' => $node->Услуга->ref])) === null) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model = new ServiceUnit();
                    $model->name = (string) $name;
                    $model->id_1c = (string) $node->Услуга->ref;
                    $model->save();
                    $transaction->commit();
                } catch (Exception $ex) {
                    $transaction->rollback();
                }
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->name = (string) $name;
                    $model->save();
                    $transaction->commit();
                } catch (Exception $ex) {
                    $transaction->rollback();
                }
            }

            $result = $model->id;
        }

        return $result;
    }

    // Get data for Service add to DB
    private static function setServiceInfo(object $node, int $serviseUnitId = 0): int {
        $result = 0;

        if (isset($node->Услуга->ref)) {

            $id1c = (string) $node->Услуга->ref;

            $name = $node->Услуга->Наименование ?? null;

            if (($model = Service::findOne(['id_1c' => $id1c])) === null) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model = new Service();
                    $model->name = (string) $name;
                    $model->id_1c = $id1c;
                    $model->service_unit_id = ($serviseUnitId > 0) ? $serviseUnitId : null;
                    $model->save();
                    $transaction->commit();
                } catch (Exception $ex) {
                    $transaction->rollback();
                }
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->name = (string) $name;
                    $model->service_unit_id = ($serviseUnitId > 0) ? $serviseUnitId : null;
                    $model->save();
                    $transaction->commit();
                } catch (Exception $ex) {
                    $transaction->rollback();
                }
            }

            $result = $model->id;
        }

        return $result;
    }

    // Get data for Counter Data add to DB
    private static function setCounterDataInfo(object $node, int $serviceId, int $flatId): int {
        $result = 0;

        if (isset($node->ПоказаниеСчетчика->ref)) {

            $uid = (string) $node->ПоказаниеСчетчика->ref;

            $uid_date = (isset($node->ПоказаниеСчетчика->ДатаСоздания)) ? date('Y-m-d', strtotime((string) $node->ПоказаниеСчетчика->ДатаСоздания)) : date('Y-m-d');
            $create_date = (isset($node->ПоказаниеСчетчика->ДатаСоздания)) ? strtotime((string) $node->ПоказаниеСчетчика->ДатаСоздания) : time();
            $update_date = (isset($node->ПоказаниеСчетчика->ДатаИзменения)) ? strtotime((string) $node->ПоказаниеСчетчика->ДатаИзменения) : time();

            if (($model = CounterData::findOne(['uid' => $uid])) === null) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model = new CounterData();
                    $model->uid = (string) $uid;
                    $model->uid_date = $uid_date;
                    $model->created_at = $create_date;
                    $model->updated_at = $update_date;
                    $model->service_id = $serviceId;
                    $model->flat_id = $flatId;
                    $model->amount_total = $model->amount = $node->ПоказаниеСчетчика->ТекущийПоказатель ?? null;
                    $model->user_admin_id = null;
                    $model->status = CounterData::STATUS_NEW;
                    $model->counter_data_last_id = null;
                    $model->save();
                    $transaction->commit();
                } catch (Exception $ex) {
                    $transaction->rollback();
                }
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->updated_at = $update_date;
                    $model->service_id = $serviceId;
                    $model->flat_id = $flatId;
                    $model->amount_total = $model->amount = $node->ПоказаниеСчетчика->ТекущийПоказатель ?? null;
                    $model->status = CounterData::STATUS_ACTIVE;
                    $model->counter_data_last_id = null;
                    $model->save();
                    $transaction->commit();
                } catch (Exception $ex) {
                    $transaction->rollback();
                }
            }

            $result = $model->id;
        }

        return $result;
    }

    // Get data for Invoce and add to DB
    private static function setInvoiceInfo(object $node, int $flatId, $tariffId = null): int {
        $result = 0;

        if (isset($node->УслугиКвитанции->СтрокаТаблицыУслуг) && isset($node->ref)) {

            if ($flatId > 0):

                $uid = (string) $node->ref;
                $uid_date = (isset($node->ДатаАкта)) ? date('Y-m-d', strtotime((string) $node->ДатаАкта)) : date('Y-m-d');
                $period_start = (isset($node->НачалоПериодаНачислений)) ? date('Y-m-d', strtotime((string) $node->НачалоПериодаНачислений)) : date('Y-m-d');
                $period_end = (isset($node->КонецПериодаНачислений)) ? date('Y-m-d', strtotime((string) $node->КонецПериодаНачислений)) : date('Y-m-d');
                $create_date = (isset($node->ДатаСоздания)) ? strtotime((string) $node->ДатаСоздания) : time();
                $update_date = (isset($node->ДатаИзменения)) ? strtotime((string) $node->ДатаИзменения) : time();

                $status = Invoice::STATUS_UNPAID;
                $isChecked = 0;

                if (($model = Invoice::findOne(['uid' => $uid])) === null) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model = new Invoice();
                        $model->uid = $uid;
                        $model->uid_date = $uid_date;
                        $model->flat_id = $flatId;
                        $model->period_start = $period_start;
                        $model->period_end = $period_end;
                        $model->created_at = $create_date;
                        $model->updated_at = $update_date;
                        $model->tariff_id = null;
                        $model->status = $status;
                        $model->is_checked = $isChecked;
                        $model->save();
                        $transaction->commit();
                        $result = $model->id;
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                } else {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $model->uid_date = $uid_date;
                        $model->flat_id = $flatId;
                        $model->period_start = $period_start;
                        $model->period_end = $period_end;
                        $model->updated_at = $update_date;
                        $model->tariff_id = $tariffId ?? $model->tariff_id;
                        $model->status = $status;
                        $model->is_checked = $isChecked ?? $model->is_checked;
                        $model->save();
                        $transaction->commit();
                        $result = (int) $model->id;
                    } catch (Exception $ex) {
                        $transaction->rollback();
                    }
                }
            endif;
        }

        return $result;
    }

    // Get data for Account Transaction and add to DB
    private static function setAccountTransactionInfo(object $node, int $invoceId, int $accountId): bool {
        $result = false;

        if (!isset($node->СтатусОплаты)) {
            return true;
        }

        $uid = (string) $node->ref;
        $date_pay = (isset($node->ДатаАкта)) ? date('Y-m-d H:i:s', strtotime((string) $node->ДатаАкта)) : date('Y-m-d H:i:s');

        // Get user ID
        if (isset($node->Контрагент)) {
            $userId = ArrayHelper::getValue(User::find()->where(['uid' => $node->Контрагент])->asArray()->one(), 'id');
        }

        if (($model = AccountTransaction::findOne(['uid' => $uid])) === null) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model = new AccountTransaction();
                $model->uid = $uid;
                $model->uid_date = $date_pay;
                $model->account_id = $accountId;
                $model->type = AccountTransaction::TYPE_OUT;
                $model->description = null;
                $model->status = AccountTransaction::STATUS_COMPLETE;
                $model->amount = floatval($node->УслугиКвитанции->СтрокаТаблицыУслуг->Сумма);
                $model->invoice_id = $invoceId;
                $model->currency_id = 1;
                $model->transaction_purpose_id = 2;
                $model->user_admin_id = null;
                $model->user_id = $userId ?? null;
                $model->invoice_service_id = null;
                $model->save();

                $transaction->commit();
                $result = true;
            } catch (Exception $ex) {
                $transaction->rollback();
            }
        } else {
            $result = true;
        }

        return $result;
    }

    // Get data for Account Transaction pay and add to DB
    private static function setPayAccountTransactionInfo(object $node): bool {
        $result = false;

        if (!isset($node->СуммаДокумента) || !isset($node->Помещение)) {
            return true;
        }

        $uid = (string) $node->ref;
        $date_pay = (isset($node->ДатаОплаты)) ? date('Y-m-d H:i:s', strtotime((string) $node->ДатаОплаты)) : date('Y-m-d H:i:s');

        // Get Account by Ref
        $accountId = ArrayHelper::getValue(Account::find()->where(['uid' => $node->Помещение])->asArray()->one(), 'id');

        // Get user ID
        if (isset($node->Контрагент)) {
            $userId = ArrayHelper::getValue(User::find()->where(['uid' => $node->Контрагент])->asArray()->one(), 'id');
        }

        if (($model = AccountTransaction::findOne(['uid' => $uid])) === null) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model = new AccountTransaction();
                $model->uid = $uid;
                $model->uid_date = $date_pay;
                $model->account_id = $accountId;
                $model->type = AccountTransaction::TYPE_IN;
                $model->description = null;
                $model->status = AccountTransaction::STATUS_COMPLETE;
                $model->amount = floatval($node->СуммаДокумента);
                $model->invoice_id = null;
                $model->currency_id = 1;
                $model->transaction_purpose_id = 2;
                $model->user_admin_id = null;
                $model->user_id = $userId ?? null;
                $model->invoice_service_id = null;
                $model->save();

                $transaction->commit();
                $result = true;
            } catch (Exception $ex) {
                $transaction->rollback();
            }
        } else {
            $result = true;
        }

        return $result;
    }

    // Get Account ID for 1C
    private static function getAccountId(object $node, int $flatId): int {
        return ArrayHelper::getValue(Account::find()->where(['uid' => (string) $node->Ref, 'flat_id' => $flatId])->asArray()->one(), 'id');
    }

    // Get random email
    private static function getEmail($length = 12) {
        if ($length < 6) {
            $length = 6;
        }
        if ($length > 125) {
            $length = 125;
        }
        $alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890';

        $email = [];
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $email[] = $alphabet[$n];
        }
        return implode($email) . '@nodomain.net';
    }

    // Send meil to user
    private static function sendInviteMail(string $email, string $password, string $uid) {
        $urlCabinet = Yii::$app->params['baseUrl'] . '/cabinet';
        $title = 'Приглашение в ' . Yii::$app->name;
        $message = 'Ваш логин: ' . $uid . ' или ваш email адрес' . $email . "\r\n" . 'Ваш пароль для входа в кабинет "' . Yii::$app->name . '":'
                . "\r\n" . $password
                . "\r\n \r\n" . 'Ссылка для входа: <a href="' . $urlCabinet . '">' . $urlCabinet . '</a>';

        \Yii::$app->mailer->compose()
                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                ->setTo($email)
                ->setSubject($title)
                ->setTextBody(strip_tags($message))
                ->setHtmlBody(nl2br($message))
                ->send();
    }

    /**
     * Change invoices status after complate import payments
     */
    static public function changeInvoicesStatus() {
        // Get acount array
        $arrAcount = ArrayHelper::getColumn(Account::find()->where(['status' => Account::STATUS_ACTIVE])->andWhere(['is not', 'flat_id', null])->groupBy(['flat_id'])->asArray()->all(), 'id');

        if (isset($arrAcount) && count($arrAcount) > 0) {
            foreach ($arrAcount as $account) :
                // Get all this account invoice transaction sum
                $sumInvoce = AccountTransaction::find()->where(['account_id' => $account, 'type' => AccountTransaction::TYPE_OUT, 'status' => AccountTransaction::STATUS_COMPLETE])->andWhere(['is not', 'invoice_id', null])->sum('amount');
            
                // Get summ of paide invoice transaction for the account
                $sumPaydeInvoce = AccountTransaction::find()->where(['account_transaction.account_id' => $account, 'account_transaction.type' => AccountTransaction::TYPE_OUT, 'account_transaction.status' => AccountTransaction::STATUS_COMPLETE, 'invoice.status' => Invoice::STATUS_PAID])->leftJoin('invoice', 'invoice.id = account_transaction.invoice_id')->andWhere(['is not', 'account_transaction.invoice_id', null])->sum('account_transaction.amount');

                if (!isset($sumPaydeInvoce)) {
                    $sumPaydeInvoce = 0;
                }

                // Get all this account payment transaction sum
                $sumPayment = AccountTransaction::find()->where(['account_id' => $account, 'type' => AccountTransaction::TYPE_IN, 'status' => AccountTransaction::STATUS_COMPLETE])->andWhere(['is', 'invoice_id', null])->sum('amount');
                
                if (isset($sumInvoce) && isset($sumPayment)) {
                    $balance = $sumPayment - $sumInvoce;
                    $lastPaySum = $sumPayment - $sumPaydeInvoce;
                }

                // Clouse all invoices if balanse more of null
                if (isset($balance) && $balance >= 0) {
                    // Get invoices ID
                    $arrInvoce = ArrayHelper::getColumn(AccountTransaction::find()->where(['account_id' => $account, 'type' => AccountTransaction::TYPE_OUT, 'status' => AccountTransaction::STATUS_COMPLETE])->andWhere(['is not', 'invoice_id', null])->asArray()->all(), 'invoice_id');

                    foreach ($arrInvoce as $invoice) {
                        $model = Invoice::findOne(['and', ['id' => $invoice], ['<>', 'status', Invoice::STATUS_PAID]]);
                        $model->status = Invoice::STATUS_PAID;
                        $model->updated_at = time();
                        $model->save();
                    }
                } else {

                    // Get data of not paide invoice transaction for the account
                    $arrNotPaydeInvoices = ArrayHelper::map(AccountTransaction::find()->select(['account_transaction.invoice_id as invoice_id', 'account_transaction.amount as amount'])->where(['account_transaction.account_id' => $account, 'account_transaction.type' => AccountTransaction::TYPE_OUT, 'account_transaction.status' => AccountTransaction::STATUS_COMPLETE, 'invoice.status' => Invoice::STATUS_UNPAID])->leftJoin('invoice', 'invoice.id = account_transaction.invoice_id')->andWhere(['is not', 'account_transaction.invoice_id', null])->asArray()->all(), 'invoice_id', 'amount');
                    foreach ($arrNotPaydeInvoices as $key => $val) {
                        if($lastPaySum >= $val) {
                            $modelInvoce = Invoice::findOne(['id' => $key]);
                            $modelInvoce->status = Invoice::STATUS_PAID;
                            $modelInvoce->save();
                            $lastPaySum -= $val;
                        }
                        else {
                            break;
                        }
                    }
                }
            endforeach;
        }
    }

}
