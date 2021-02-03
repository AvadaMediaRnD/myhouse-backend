<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use XMLReader;
use DOMDocument;
use SimpleXMLElement;
use common\models\Invoice;
use common\models\InvoiceService;
use common\models\CounterData;
use common\models\House;
use common\models\Flat;
use common\models\User;
use common\models\Profile;
use common\models\Tariff;

class OneCComponent extends Component 
{
    public $fileData = '';
    
    public function welcome()
    {
        echo "Hello..Welcome to MyComponent";
    }
    
    public function updateData()
    {
        $fileDataFullPath = Yii::getAlias('@app/../') . $this->fileData;
        var_dump($fileDataFullPath);
        if (!$this->fileData || !file_exists($fileDataFullPath) || !is_readable($fileDataFullPath)) {
            var_dump('wrong file');
        }
        
        $xml = simplexml_load_string(file_get_contents($fileDataFullPath));
        $xmlArray = [$xml->getName() => json_decode(json_encode((array) $xml), true)];
        
        $attributesOrg = $xmlArray['ORG']['@attributes'];
        // var_dump($attributesOrg);
        
        foreach ($xmlArray['ORG']['PersAcc'] as $accArray) {
            $attributesAcc = $accArray['@attributes'];
            // var_dump($attributesAcc);
            
            $invoiceModel = $this->generateInvoiceFromData($attributesAcc, $attributesOrg);
            
            if (isset($accArray['item']) && is_array($accArray['item'])) {
                foreach ($accArray['item'] as $itemArray) {
                    $attributesItem = $itemArray['@attributes'];
                    var_dump($attributesItem);
                    
                    $invoiceServiceModel = $this->generateInvoiceServiceFromData($attributesItem, $invoiceModel);
                }
            }
            
            if (isset($accArray['meter']) && is_array($accArray['meter'])) {
                foreach ($accArray['meter'] as $meterArray) {
                    $attributesMeter = $meterArray['@attributes'];
                    // var_dump($attributesMeter);
                    
                    $counterDataModel = $this->generateCounterDataFromData($attributesMeter, $invoiceModel);
                }
            }
        }
        
        // var_dump($xmlArray);
    }
    
    /**
     * 
     * @param array $data
     * @return Invoice
     */
    public function generateInvoiceFromData($data, $dataDocument = null)
    {
        $model = new Invoice();
        
        // var_dump($data);
        
        $model->uid = $data['kod_ls'];
        if ($dataDocument) {
            $model->uid_date = date('Y-m-d', strtotime($dataDocument['filedate']));
            $model->period_end = date('Y-m-d', strtotime($dataDocument['filedate']));
            $model->period_start = date('Y-m-d', strtotime($dataDocument['filedate']));
        }
        
        // house address
        $addressLines = [
            'AddressRegion' => $data['AddressRegion'],
            'AddressCity' => $data['AddressCity'],
            'AddressDistrict' => $data['AddressDistrict'],
            'AddressSettlement' => $data['AddressSettlement'],
            'AddressStreet' => $data['AddressStreet'],
            'AddressHouse' => $data['AddressHouse'],
        ];
        // user data
        $userLines = [
            'login' => $data['login'],
            'email' => $data['email'],
            'name' => $data['name'],
            'name_ls' => $data['name_ls'],
        ];
        
        $house = $this->getHouseByAddress($addressLines);
        $user = $this->getUserByData($userLines);
        $flat = $this->getFlatByHouseUserNumber($house, $user, $data['AddressFlat']);
        
        $model->flat_id = $flat->id;
        $model->status = Invoice::STATUS_UNPAID;
        $model->tariff_id = Tariff::findDefaultModel()->id;
        $model->save();
        
        return $model;
    }
    
    /**
     * 
     * @param array $data
     * @param Invoice $invoiceModel
     * @return InvoiceService
     */
    public function generateInvoiceServiceFromData($data, $invoiceModel = null)
    {
        $model = new InvoiceService();
        
        return $model;
    }
    
    /**
     * 
     * @param array $data
     * @param Invoice $invoiceModel
     * @param InvoiceService $invoiceServiceModel
     * @return CounterData
     */
    public function generateCounterDataFromData($data, $invoiceModel = null, $invoiceServiceModel = null)
    {
        $model = new CounterData();
        
        return $model;
    }
    
    /**
     * 
     * @param array $addressLines
     * @return House
     */
    private function getHouseByAddress($addressLines)
    {
        $addressLines = array_filter($addressLines);
        $address = implode(', ', $addressLines);
        $name = implode(', ', [$addressLines['AddressStreet'], $addressLines['AddressHouse']]);
        
        $query = House::find()->where(['address' => $address]);
        if ($query->exists()) {
            return $query->one();
        }
        $model = new House();
        $model->address = $address;
        $model->name = $name;
        $model->save();
        return $model;
    }
    
    /**
     * 
     * @param array $userLines
     * @return User
     */
    private function getUserByData($userLines)
    {
        $query = User::find();
        if ($userLines['login']) {
            $query->andWhere(['or', ['email' => $userLines['login']], ['uid' => $userLines['login']]]);
        }
        if ($userLines['email']) {
            $query->andWhere(['email' => $userLines['email']]);
        }
        if ($userLines['name_ls']) {
            $query->andWhere(['uid' => $userLines['name_ls']]);
        }
        
        $nameParts = explode(' ', $userLines['name']);
        
        if ($userLines['name']) {
            $query->joinWith('profile');
            $query->andWhere(new \yii\db\Expression('CONCAT(`profile`.`lastname`, " ", `profile`.`firstname`,  " ", `profile`.`middlename`) = "' . $userLines['name'] . '"'));
        }
        if ($query->exists()) {
            return $query->one();
        }
        
        $model = new User();
        $model->email = $userLines['email'] ?: $userLines['name_ls'];
        $model->uid = $userLines['name_ls'];
        $password = date('ymd'); // set default pass
        $model->setPassword($password);
        $model->generateAuthKey();
        if ($model->save()) {
            $profile = new Profile();
            $profile->user_id = $model->id;
            $profile->lastname = isset($nameParts[0]) ? $nameParts[0] : '';
            $profile->firstname = isset($nameParts[1]) ? $nameParts[1] : '';
            $profile->middlename = isset($nameParts[2]) ? $nameParts[2] : '';
            $profile->save();
        }
        return $model;
    }
    
    /**
     * 
     * @param House $houseModel
     * @param string $flatNumber
     */
    private function getFlatByHouseUserNumber($houseModel, $userModel, $flatNumber)
    {
        $query = Flat::find()->where(['flat' => $flatNumber, 'house_id' => $houseModel->id]);
        if ($query->exists()) {
            $model = $query->one();
            if ($userModel->id && !$flat->user_id) {
                $model->user_id = $userModel->id;
                $model->save();
            }
            return $model;
        }
        $model = new Flat();
        $model->flat = $flatNumber;
        $model->house_id = $houseModel->id;
        $model->user_id = $userModel->id;
        $model->save();
        return $model;
    }

}
