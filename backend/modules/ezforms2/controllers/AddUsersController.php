<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use backend\modules\ezforms2\models\CNProfileTcc;
use backend\modules\ezforms2\models\CNProfileTccSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * AddUsersController implements the CRUD actions for ProfileTcc model.
 */
class AddUsersController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            if (in_array($action->id, array('create', 'update'))) {
                
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lists all ProfileTcc models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CNProfileTccSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dbType = isset($_GET['db_type']) ? $_GET['db_type'] : "";
        return $this->redirect(['create', 'db_type' => $dbType]);
//        return $this->render('index', [
//            'searchModel' => $searchModel,
//            'dataProvider' => $dataProvider,
//        ]);
    }

    /**
     * Displays a single ProfileTcc model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        if (Yii::$app->getRequest()->isAjax) {
            return $this->renderAjax('view', [
                        'model' => $this->findModel($id),
            ]);
        } else {
            return $this->render('view', [
                        'model' => $this->findModel($id),
            ]);
        }
    }

    public function actionCreate() {
        $dbType = isset($_GET['db_type']) ? $_GET['db_type'] : "";
        $model = new CNProfileTcc();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $profilePost = $_POST['CNProfileTcc'];
              
            $user_id = isset($profilePost['user_id']) ? $profilePost['user_id'] : "";
            $site_code = isset($profilePost['sitecode']) ? $profilePost['sitecode'] : "";
            $department = isset($profilePost['department']) ? $profilePost['department'] : "";
           
            $type = isset($_POST['type']) ? $_POST['type'] : "";
            $status = $this->Create($user_id, $site_code, $type, $department);
            if ($status == 1) {
                //success
                //\cpn\chanpan\widgets\CNWizards::widget();
                return \backend\modules\manageproject\classes\CNMessage::getSuccess("Data completed.");
            } else if ($status == 2) {
                //not found user 
                return \backend\modules\manageproject\classes\CNMessage::getError("No users in the system");
            } else if ($status == 3) {
                return \backend\modules\manageproject\classes\CNMessage::getError("Already have this user.");
            } else {
                //not success
                return \backend\modules\manageproject\classes\CNMessage::getError("Already have this user.");
            }
        } else {

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('create', [
                            'model' => $model,
                            'db_type' => $dbType,
                            'dataUser' => '',
                ]);
            }
        }
    }

    private function Create($user_id, $site_code, $type, $department) {
        $status = 0; //not save 
        try {
            $sql = "";
            $data = "";
            $type = isset($type) ? $type : "tcc";
            if ($type == "tcc") {
                $sql = "SELECT * FROM user as u INNER JOIN user_profile as p ON u.id = p.user_id WHERE u.id = :user_id";
                $data = \Yii::$app->db_tcc->createCommand($sql, [':user_id' => $user_id])->queryOne();
            } else {
                $sql = "SELECT * FROM `user` as `u` INNER JOIN `profile` as `p` ON u.id = p.user_id WHERE u.id = :user_id";
                $data = \Yii::$app->db_main->createCommand($sql, [':user_id' => $user_id])->queryOne();
            }
            if (!empty($data)) {
                $dataUserAttribute = [
                    'id' => $data["user_id"],
                    'username' => $data['username'],
                    'email' => isset($data['email']) ? $data['email'] : " ",
                    'password_hash' => $data['password_hash'],
                    'auth_key' => $data['auth_key'],
                    'confirmed_at' => time(),
                    'created_at' => time(),
                    'updated_at' => time(),
                    'flags' => 0
                ];
                $saveUser = \Yii::$app->db->createCommand()->insert("user", $dataUserAttribute)->execute();
                if ($saveUser) {
                    \cpn\chanpan\classes\CNUser::saveUserProject($data["user_id"]);
                    
                    $dataProfileAttribuite = [
                        'user_id' => $user_id,
                        'public_email' => isset($data['email']) ? $data['email'] : " ",
                        'tel' => isset($data['telephone']) ? $data['telephone'] : " ",
//                        'cid' => isset($data['cid']) ? $data["cid"] : "",
                        'sitecode' => isset($site_code) ? $site_code : '00',
                        'firstname' => isset($data['firstname']) ? $data['firstname'] : '',
                        'lastname' => isset($data['lastname']) ? $data['lastname'] : '',
                        'department' => isset($department) ? $department : '00',
                        'certificate' => ' ',
                        'position' => 0
                    ];
                    if ($type == "tcc") {
                        $dataProfileAttribuite['sitecode'] = '00';
                    }
                    $saveProfile = \Yii::$app->db->createCommand()->insert("profile", $dataProfileAttribuite)->execute();
                    if ($saveProfile) {
                        if ($type == "tcc") {
                            \Yii::$app->db_main->createCommand()->insert('user', $dataUserAttribute)->execute();
                            \Yii::$app->db_main->createCommand()->insert('profile', $dataProfileAttribuite)->execute();
                        }
                        $checkRole = (new \yii\db\Query())->select('*')->from('auth_assignment')->where(['user_id' => $user_id])->all();
                        if (!empty($checkRole)) {
                            $deleteRole = \Yii::$app->db->createCommand("DELETE FROM auth_assignment WHERE user_id=:user_id", [
                                        ':user_id' => $user_id
                                    ])->execute();
                        }
                        $dataRole = ['item_name' => "author", 'user_id' => $user_id, 'created_at' => time()];
                        $saveRole = \Yii::$app->db->createCommand()->insert("auth_assignment", $dataRole)->execute();
                        $status = 1; //save success
                    }
                } else {
                    $status = 3; //Already have this user.
                }
            } else {
                $status = 2; //not found user ????
            }

            return $status;
        } catch (\yii\db\Exception $e) {
            return $e->getMessage();
        }
    }

    protected function findModel($id) {
        if (($model = ProfileTcc::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUserNcrc() {
        return $this->render("user-ncrc");
    }

    public function actionGetSitecode() {
        $user_id = isset(\Yii::$app->user->id) ? \Yii::$app->user->id : $_GET['user_id'];//isset($_GET['user_id']) ? $_GET['user_id'] : '';
        $sql = "SELECT `profile`.sitecode FROM `profile` WHERE `profile`.`user_id` = :user_id";
        $dataSitecode = Yii::$app->db->createCommand($sql, [':user_id'=>$user_id])->queryOne();
        $dataSitecode = isset($dataSitecode['sitecode']) ? $dataSitecode['sitecode'] : '';
        
        $user = \cpn\chanpan\classes\CNUser::GetSiteCodeByUserId($user_id, $dataSitecode);
        $data = [];
        if (empty($user)) {
            $data['status'] = 'success';
            $data['results'] = ['id' => '', 'name' => ''];
        } else {
            $data['status'] = 'success';
            $data['results'] = $user;
        }
        //Yii::$app->response->format = Response::FORMAT_JSON;
        return json_encode($data);
    }

    public function actionDepartMent() {
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
        $user = \cpn\chanpan\classes\CNUser::GetSiteCodeByUserId($user_id);
        $data = [];
        if (empty($user)) {
            $data['status'] = 'success';
            $data['results'] = ['id' => '', 'name' => ''];
        } else {
            $data['status'] = 'success';
            $data['results'] = $user;
        }
        //Yii::$app->response->format = Response::FORMAT_JSON;
        return json_encode($data);
    }

}
