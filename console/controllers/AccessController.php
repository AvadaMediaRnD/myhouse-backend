<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\UserAdmin;
use common\models\MasterRequest;

/**
 * AccessController
 */
class AccessController extends Controller
{
    public function actionInit()     
    {         
	$auth = Yii::$app->authManager;
	$auth->removeAll(); //remove previous rbac.php files under console/data
	 
        //CREATE PERMISSIONS

        $siteView = $auth->createPermission('siteView');
        $siteView->description = 'Site View';
        $auth->add($siteView);
	
        $accountView = $auth->createPermission('accountView');
        $accountView->description = 'Account View';
        $auth->add($accountView);

        $accountUpdate = $auth->createPermission('accountUpdate');
        $accountUpdate->description = 'Account Update';
        $auth->add($accountUpdate);

        $accountDelete = $auth->createPermission('accountDelete');
        $accountDelete->description = 'Account Delete';
        $auth->add($accountDelete);
        
        $accountTransactionView = $auth->createPermission('accountTransactionView');
        $accountTransactionView->description = 'Account Transaction View';
        $auth->add($accountTransactionView);

        $accountTransactionUpdate = $auth->createPermission('accountTransactionUpdate');
        $accountTransactionUpdate->description = 'Account Transaction Update';
        $auth->add($accountTransactionUpdate);

        $accountTransactionDelete = $auth->createPermission('accountTransactionDelete');
        $accountTransactionDelete->description = 'Account Transaction Delete';
        $auth->add($accountTransactionDelete);
        
        $userView = $auth->createPermission('userView');
        $userView->description = 'User View';
        $auth->add($userView);

        $userUpdate = $auth->createPermission('userUpdate');
        $userUpdate->description = 'User Update';
        $auth->add($userUpdate);

        $userDelete = $auth->createPermission('userDelete');
        $userDelete->description = 'User Delete';
        $auth->add($userDelete);
        
        $houseView = $auth->createPermission('houseView');
        $houseView->description = 'House View';
        $auth->add($houseView);

        $houseUpdate = $auth->createPermission('houseUpdate');
        $houseUpdate->description = 'House Update';
        $auth->add($houseUpdate);

        $houseDelete = $auth->createPermission('houseDelete');
        $houseDelete->description = 'House Delete';
        $auth->add($houseDelete);
        
        $flatView = $auth->createPermission('flatView');
        $flatView->description = 'Flat View';
        $auth->add($flatView);

        $flatUpdate = $auth->createPermission('flatUpdate');
        $flatUpdate->description = 'Flat Update';
        $auth->add($flatUpdate);

        $flatDelete = $auth->createPermission('flatDelete');
        $flatDelete->description = 'Flat Delete';
        $auth->add($flatDelete);
        
        $messageView = $auth->createPermission('messageView');
        $messageView->description = 'Message View';
        $auth->add($messageView);

        $messageUpdate = $auth->createPermission('messageUpdate');
        $messageUpdate->description = 'Message Update';
        $auth->add($messageUpdate);

        $messageDelete = $auth->createPermission('messageDelete');
        $messageDelete->description = 'Message Delete';
        $auth->add($messageDelete);
        
        $masterRequestView = $auth->createPermission('masterRequestView');
        $masterRequestView->description = 'Master Request View';
        $auth->add($masterRequestView);

        $masterRequestUpdate = $auth->createPermission('masterRequestUpdate');
        $masterRequestUpdate->description = 'Master Request Update';
        $auth->add($masterRequestUpdate);

        $masterRequestDelete = $auth->createPermission('masterRequestDelete');
        $masterRequestDelete->description = 'Master Request Delete';
        $auth->add($masterRequestDelete);
        
        $invoiceView = $auth->createPermission('invoiceView');
        $invoiceView->description = 'Invoice View';
        $auth->add($invoiceView);

        $invoiceUpdate = $auth->createPermission('invoiceUpdate');
        $invoiceUpdate->description = 'Invoice Update';
        $auth->add($invoiceUpdate);

        $invoiceDelete = $auth->createPermission('invoiceDelete');
        $invoiceDelete->description = 'Invoice Delete';
        $auth->add($invoiceDelete);
        
        $counterDataView = $auth->createPermission('counterDataView');
        $counterDataView->description = 'Counter Data View';
        $auth->add($counterDataView);

        $counterDataUpdate = $auth->createPermission('counterDataUpdate');
        $counterDataUpdate->description = 'Counter Data Update';
        $auth->add($counterDataUpdate);

        $counterDataDelete = $auth->createPermission('counterDataDelete');
        $counterDataDelete->description = 'Counter Data Delete';
        $auth->add($counterDataDelete);
        
        $websiteUpdate = $auth->createPermission('websiteUpdate');
        $websiteUpdate->description = 'Website Update';
        $auth->add($websiteUpdate);
        
        $serviceView = $auth->createPermission('serviceView');
        $serviceView->description = 'Service View';
        $auth->add($serviceView);

        $serviceUpdate = $auth->createPermission('serviceUpdate');
        $serviceUpdate->description = 'Service Update';
        $auth->add($serviceUpdate);

        $serviceDelete = $auth->createPermission('serviceDelete');
        $serviceDelete->description = 'Service Delete';
        $auth->add($serviceDelete);
        
        $tariffView = $auth->createPermission('tariffView');
        $tariffView->description = 'Tariff View';
        $auth->add($tariffView);

        $tariffUpdate = $auth->createPermission('tariffUpdate');
        $tariffUpdate->description = 'Tariff Update';
        $auth->add($tariffUpdate);

        $tariffDelete = $auth->createPermission('tariffDelete');
        $tariffDelete->description = 'Tariff Delete';
        $auth->add($tariffDelete);
        
        $roleUpdate = $auth->createPermission('roleUpdate');
        $roleUpdate->description = 'Role Update';
        $auth->add($roleUpdate);

        $userAdminView = $auth->createPermission('userAdminView');
        $userAdminView->description = 'User Admin View';
        $auth->add($userAdminView);

        $userAdminUpdate = $auth->createPermission('userAdminUpdate');
        $userAdminUpdate->description = 'User Admin Update';
        $auth->add($userAdminUpdate);

        $userAdminDelete = $auth->createPermission('userAdminDelete');
        $userAdminDelete->description = 'User Admin Delete';
        $auth->add($userAdminDelete);
        
        $payCompanyUpdate = $auth->createPermission('payCompanyUpdate');
        $payCompanyUpdate->description = 'Pay Company Update';
        $auth->add($payCompanyUpdate);
        
        // PERMISSIONS PACKS
        $site = $auth->createPermission('site');
        $site->description = 'Site';
        $auth->add($site);
        $auth->addChild($site, $siteView);
        
        $account = $auth->createPermission('account');
        $account->description = 'Account';
        $auth->add($account);
        $auth->addChild($account, $accountView);
        $auth->addChild($account, $accountUpdate);
        $auth->addChild($account, $accountDelete);
        
        $accountTransaction = $auth->createPermission('accountTransaction');
        $accountTransaction->description = 'Account Transaction';
        $auth->add($accountTransaction);
        $auth->addChild($accountTransaction, $accountTransactionView);
        $auth->addChild($accountTransaction, $accountTransactionUpdate);
        $auth->addChild($accountTransaction, $accountTransactionDelete);
        
        $user = $auth->createPermission('user');
        $user->description = 'User';
        $auth->add($user);
        $auth->addChild($user, $userView);
        $auth->addChild($user, $userUpdate);
        $auth->addChild($user, $userDelete);
        
        $house = $auth->createPermission('house');
        $house->description = 'House';
        $auth->add($house);
        $auth->addChild($house, $houseView);
        $auth->addChild($house, $houseUpdate);
        $auth->addChild($house, $houseDelete);
        
        $flat = $auth->createPermission('flat');
        $flat->description = 'Flat';
        $auth->add($flat);
        $auth->addChild($flat, $flatView);
        $auth->addChild($flat, $flatUpdate);
        $auth->addChild($flat, $flatDelete);
        
        $message = $auth->createPermission('message');
        $message->description = 'Message';
        $auth->add($message);
        $auth->addChild($message, $messageView);
        $auth->addChild($message, $messageUpdate);
        $auth->addChild($message, $messageDelete);
        
        $masterRequest = $auth->createPermission('masterRequest');
        $masterRequest->description = 'Master Request';
        $auth->add($masterRequest);
        $auth->addChild($masterRequest, $masterRequestView);
        $auth->addChild($masterRequest, $masterRequestUpdate);
        $auth->addChild($masterRequest, $masterRequestDelete);
        
        $invoice = $auth->createPermission('invoice');
        $invoice->description = 'Invoice';
        $auth->add($invoice);
        $auth->addChild($invoice, $invoiceView);
        $auth->addChild($invoice, $invoiceUpdate);
        $auth->addChild($invoice, $invoiceDelete);
        
        $counterData = $auth->createPermission('counterData');
        $counterData->description = 'Counter Data';
        $auth->add($counterData);
        $auth->addChild($counterData, $counterDataView);
        $auth->addChild($counterData, $counterDataUpdate);
        $auth->addChild($counterData, $counterDataDelete);
        
        $website = $auth->createPermission('website');
        $website->description = 'Website';
        $auth->add($website);
        $auth->addChild($website, $websiteUpdate);
        
        $system = $auth->createPermission('system');
        $system->description = 'System';
        $auth->add($system);
        $auth->addChild($system, $serviceView);
        $auth->addChild($system, $serviceUpdate);
        $auth->addChild($system, $serviceDelete);
        $auth->addChild($system, $tariffView);
        $auth->addChild($system, $tariffUpdate);
        $auth->addChild($system, $tariffDelete);
        $auth->addChild($system, $roleUpdate);
        $auth->addChild($system, $userAdminView);
        $auth->addChild($system, $userAdminUpdate);
        $auth->addChild($system, $userAdminDelete);
        $auth->addChild($system, $payCompanyUpdate);
        
        $service = $auth->createPermission('service');
        $service->description = 'Service';
        $auth->add($service);
        $auth->addChild($service, $serviceView);
        $auth->addChild($service, $serviceUpdate);
        $auth->addChild($service, $serviceDelete);
        
        $tariff = $auth->createPermission('tariff');
        $tariff->description = 'Tariff';
        $auth->add($tariff);
        $auth->addChild($tariff, $tariffView);
        $auth->addChild($tariff, $tariffUpdate);
        $auth->addChild($tariff, $tariffDelete);
        
        $role = $auth->createPermission('role');
        $role->description = 'Role';
        $auth->add($role);
        $auth->addChild($role, $roleUpdate);

        $userAdmin = $auth->createPermission('userAdmin');
        $userAdmin->description = 'User Admin';
        $auth->add($userAdmin);
        $auth->addChild($userAdmin, $userAdminView);
        $auth->addChild($userAdmin, $userAdminUpdate);
        $auth->addChild($userAdmin, $userAdminDelete);
        
        $payCompany = $auth->createPermission('payCompany');
        $payCompany->description = 'Pay Company';
        $auth->add($payCompany);
        $auth->addChild($payCompany, $payCompanyUpdate);
        
        //ROLES
        $roleElectrician = $auth->createRole(UserAdmin::ROLE_ELECTRICIAN);
        $auth->add($roleElectrician);
        $auth->addChild($roleElectrician, $counterDataView);
        $auth->addChild($roleElectrician, $counterDataUpdate);
        $auth->addChild($roleElectrician, $counterDataDelete);
        $auth->addChild($roleElectrician, $masterRequestView);
        
        $rolePlumber = $auth->createRole(UserAdmin::ROLE_PLUMBER);
        $auth->add($rolePlumber);
        $auth->addChild($rolePlumber, $roleElectrician);
        
        $roleAccountant = $auth->createRole(UserAdmin::ROLE_ACCOUNTANT);
        $auth->add($roleAccountant);
        $auth->addChild($roleAccountant, $roleElectrician);
        $auth->removeChild($roleAccountant, $masterRequestView);
        $auth->addChild($roleAccountant, $accountView);
        $auth->addChild($roleAccountant, $accountUpdate);
        $auth->addChild($roleAccountant, $accountDelete);
        $auth->addChild($roleAccountant, $accountTransactionView);
        $auth->addChild($roleAccountant, $accountTransactionUpdate);
        $auth->addChild($roleAccountant, $accountTransactionDelete);
        $auth->addChild($roleAccountant, $invoiceView);
        $auth->addChild($roleAccountant, $invoiceUpdate);
        $auth->addChild($roleAccountant, $invoiceDelete);
        $auth->addChild($roleAccountant, $tariffView);
        $auth->addChild($roleAccountant, $tariffUpdate);
        $auth->addChild($roleAccountant, $tariffDelete);
        $auth->addChild($roleAccountant, $serviceView);
        $auth->addChild($roleAccountant, $serviceUpdate);
        $auth->addChild($roleAccountant, $serviceDelete);
        $auth->addChild($roleAccountant, $payCompanyUpdate);
        $auth->addChild($roleAccountant, $flatView);
        $auth->addChild($roleAccountant, $userView);
        
        $roleManager = $auth->createRole(UserAdmin::ROLE_MANAGER);
        $auth->add($roleManager);
        $auth->addChild($roleManager, $roleAccountant);
        $auth->addChild($roleManager, $roleElectrician);
        $auth->addChild($roleManager, $siteView);
        $auth->addChild($roleManager, $userUpdate);
        $auth->addChild($roleManager, $userDelete);
        $auth->addChild($roleManager, $flatUpdate);
        $auth->addChild($roleManager, $flatDelete);
        $auth->addChild($roleManager, $houseView);
        $auth->addChild($roleManager, $houseUpdate);
        $auth->addChild($roleManager, $houseDelete);
        $auth->addChild($roleManager, $masterRequestUpdate);
        $auth->addChild($roleManager, $masterRequestDelete);
        $auth->addChild($roleManager, $userAdminView);
        $auth->addChild($roleManager, $userAdminUpdate);
        $auth->addChild($roleManager, $userAdminDelete);
        $auth->addChild($roleManager, $roleUpdate);

        $roleAdmin = $auth->createRole(UserAdmin::ROLE_ADMIN);
        $auth->add($roleAdmin);
        $auth->addChild($roleAdmin, $roleManager);
        $auth->addChild($roleAdmin, $websiteUpdate);

	foreach (UserAdmin::find()->all() as $user) {
            if ($user->role == UserAdmin::ROLE_ELECTRICIAN) {
                $auth->assign($roleElectrician, $user->id);
            } elseif ($user->role == UserAdmin::ROLE_PLUMBER) {
                $auth->assign($rolePlumber, $user->id);
            } elseif ($user->role == UserAdmin::ROLE_ACCOUNTANT) {
                $auth->assign($roleAccountant, $user->id);
            } elseif ($user->role == UserAdmin::ROLE_MANAGER) {
                $auth->assign($roleManager, $user->id);
            } elseif ($user->role == UserAdmin::ROLE_ADMIN) {
                $auth->assign($roleAdmin, $user->id);
            }
        }
    }
}
