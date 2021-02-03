<?php
namespace common\models\validators;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Exception;

/**
 * @inheritdoc
 */
class PhoneValidator extends \udokmeci\yii2PhoneValidator\PhoneValidator
{
    public $numberFormat = PhoneNumberFormat::E164;

    public function validateAttribute($model, $attribute)
    {

        if(!isset($country) && isset($this->countryAttribute)){
            $countryAttribute=$this->countryAttribute;
            $country=$model->$countryAttribute;
        }


        if(!isset($country) && isset($this->country)){
            $country=$this->country;
        }
   	

        if(!isset($country) && isset($model->country_code))
    		$country=$model->country_code;

        if(!isset($country) && isset($model->country))
            $country=$model->country;
        


    	if(!isset($country) && $this->strict){
    		 $this->addError($model, $attribute, \Yii::t('app','For phone validation country required'));
    		 return false;
    	}

        if(!isset($country)){
            return true;
        }

    	$phoneUtil = PhoneNumberUtil::getInstance();
    	try {
            $numberProto = $phoneUtil->parse($model->$attribute, $country);


            if ($numberProto && strlen($numberProto->getNationalNumber()) == 9) {
                return true;
            }

            if($phoneUtil->isValidNumber($numberProto)){
                if($this->format==true)
                $model->$attribute = $phoneUtil->format($numberProto, $this->numberFormat);
                return true;
            }
            else{
                $this->addError($model, $attribute, \Yii::t('app','Phone number does not seem to be a valid phone number'));
                return false;
            }

        } catch (NumberParseException $e) {
        	$this->addError($model, $attribute, \Yii::t('app','Unexpected Phone Number Format'));
        } catch (Exception $e) {
            $this->addError($model, $attribute, \Yii::t('app','Unexpected Phone Number Format or Country Code'));
        }   
    }
}