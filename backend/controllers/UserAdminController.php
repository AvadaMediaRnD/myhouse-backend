<?php

namespace backend\controllers;

use backend\models\UserAdminSearch;
use common\models\UserAdmin;
use backend\models\UserAdminLogSearch;
use common\models\UserAdminLog;
use Yii;
use backend\controllers\ZController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\UserAdminForm;
use yii\data\Pagination;

/**
 * UserAdminController implements the CRUD actions for UserAdmin model.
 */
class UserAdminController extends ZController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        $actions = $this->getAllowedActions();
        $actions[] = 'update-my';
        $actions[] = 'role'; // check access in role action
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_USER_ADMIN) && !in_array($action->id, $actions)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all UserAdmin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserAdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserAdmin model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserAdmin();
        $post = Yii::$app->request->post();
        
        $modelForm = new UserAdminForm();
        $modelForm->status = UserAdmin::STATUS_NEW;
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserAdmin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        
        if (Yii::$app->user->id != $id 
            && Yii::$app->user->identity->role != UserAdmin::ROLE_ADMIN 
            && (Yii::$app->user->identity->role != UserAdmin::ROLE_MANAGER || (Yii::$app->user->identity->role == UserAdmin::ROLE_MANAGER && $model->role == UserAdmin::ROLE_ADMIN))
        ) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        
        $modelForm = UserAdminForm::loadFromModel($model);
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                return $this->redirect(['index']);
            }
        }
        
        $model = $this->findModel($id);

        return $this->render('update', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]);
    }

    /**
     * Updates current logged in UserAdmin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUpdateMy()
    {
        $model = $this->findModel(Yii::$app->user->id);
        $post = Yii::$app->request->post();
        
        $modelForm = UserAdminForm::loadFromModel($model);
        if ($modelForm->load($post)) {
            if ($model = $modelForm->process()) {
                return $this->redirect(['update-my']);
            }
        }
        
        $model = $this->findModel(Yii::$app->user->id);

        return $this->render('update', [
            'modelForm' => $modelForm,
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing UserAdmin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->id != Yii::$app->user->id) {
            $model->delete();
        }

        return $this->redirect(['index']);
    }
    
    /**
     * Invite UserAdmin again.
     * @return mixed
     */
    public function actionInvite($id)
    {
        $model = $this->findModel($id);
        $model->sendInvite();
        return $this->redirect(['index']);
    }
    
    /**
     * 
     * @return mixed
     */
    public function actionRole()
    {
        if (!Yii::$app->user->can(UserAdmin::PERMISSION_ROLE)) {
            // return $this->redirect(['/site/error']);
            throw new \yii\web\ForbiddenHttpException(Yii::t('app', static::FORBIDDEN_HTTP_MESSAGE));
        }
        
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $auth = Yii::$app->authManager;
            
            $roles = $auth->getRoles();

            foreach ($roles as $role) {
                $accessRole = $auth->getRole($role->name);
                $auth->removeChildren($accessRole);
                
                $permissions = [];
                if ($post[UserAdmin::PERMISSION_SITE][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_SITE),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_ACCOUNT][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_ACCOUNT),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_ACCOUNT_TRANSACTION][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_ACCOUNT_TRANSACTION),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_USER][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_USER),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_HOUSE][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_HOUSE),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_FLAT][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_FLAT),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_MESSAGE][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_MESSAGE),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_MASTER_REQUEST][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_MASTER_REQUEST),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_INVOICE][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_INVOICE),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_COUNTER_DATA][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_COUNTER_DATA),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_WEBSITE][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_WEBSITE),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_SYSTEM][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_SYSTEM),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_SERVICE][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_SERVICE),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_TARIFF][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_TARIFF),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_ROLE][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_ROLE),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_USER_ADMIN][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_USER_ADMIN),
                    ]);
                }
                if ($post[UserAdmin::PERMISSION_PAY_COMPANY][$role->name]) {
                    $permissions = array_merge($permissions, [
                        $auth->getPermission(UserAdmin::PERMISSION_PAY_COMPANY),
                    ]);
                }
                
                if ($permissions) {
                    foreach ($permissions as $permission) {
                        if (!$auth->hasChild($accessRole, $permission)) {
                            $auth->addChild($accessRole, $permission);
                        } else {
                            $auth->removeChild($accessRole, $permission);
                        }
                    }
                }
            }

            $auth->removeAllAssignments();
            
            foreach (UserAdmin::find()->all() as $user) {
                if ($user->role == UserAdmin::ROLE_ELECTRICIAN) {
                    $auth->assign($auth->getRole(UserAdmin::ROLE_ELECTRICIAN), $user->id);
                } elseif ($user->role == UserAdmin::ROLE_PLUMBER) {
                    $auth->assign($auth->getRole(UserAdmin::ROLE_PLUMBER), $user->id);
                } elseif ($user->role == UserAdmin::ROLE_ACCOUNTANT) {
                    $auth->assign($auth->getRole(UserAdmin::ROLE_ACCOUNTANT), $user->id);
                } elseif ($user->role == UserAdmin::ROLE_MANAGER) {
                    $auth->assign($auth->getRole(UserAdmin::ROLE_MANAGER), $user->id);
                } elseif ($user->role == UserAdmin::ROLE_ADMIN) {
                    $auth->assign($auth->getRole(UserAdmin::ROLE_ADMIN), $user->id);
                }
            }
            
            return $this->redirect(['role']);
        }
        
        return $this->render('role', [
            'roles' => UserAdmin::getRoleOptions(),
        ]);
    }
    
    /**
     * Show changes log.
     * @return mixed
     */
    public function actionLog()
    {
        $searchModel = new UserAdminLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $query = clone $dataProvider->query;
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 100]);
        $pages->pageSizeParam = false;
        
        $query->offset($pages->offset)->limit($pages->limit);
        
        $timeLineData = [];
        foreach ($query->all() as $model) {
            if (!isset($timeLineData[$model->createdDate])) {
                $timeLineData[$model->createdDate] = [];
            }
            
            $icon = 'fa fa-envelope';
            $bg = 'bg-default';
            $title = 'Изменение';
            $user = $model->userAdmin->fullname;
            $userId = $model->user_admin_id;
            $time = $model->createdTime;
            $text = $model->message;
            if ($model->event == UserAdminLog::LOG_INSERT) {
                $icon = 'fa fa-plus';
                $bg = 'bg-green';
                $title = 'Добавлен объект';
            } elseif ($model->event == UserAdminLog::LOG_UPDATE) {
                $icon = 'fa fa-pencil';
                $bg = 'bg-blue';
                $title = 'Изменен объект';
            } elseif ($model->event == UserAdminLog::LOG_DELETE) {
                $icon = 'fa fa-trash';
                $bg = 'bg-red';
                $title = 'Удален объект';
            }
            
            array_unshift($timeLineData[$model->createdDate], [
                'icon' => $icon,
                'bg' => $bg,
                'title' => $title,
                'user' => $user,
                'userId' => $userId,
                'time' => $time,
                'text' => $text,
            ]);
        }

        return $this->render('log', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'timeLineData' => $timeLineData,
            'pages' => $pages,
        ]);
    }

    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserAdmin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserAdmin::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
