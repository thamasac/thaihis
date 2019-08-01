<?php

namespace backend\modules\core\controllers;

use Yii;
use backend\modules\core\models\FileStorageItem;
use backend\modules\core\models\FileStorageItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * FileStorageController implements the CRUD actions for FileStorageItem model.
 */
class FileStorageController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'upload-delete' => ['delete']
                ]
            ]
        ];
    }


    public function actions()
    {
        return [
            'upload' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                'deleteRoute' => 'upload-delete'
            ],
            'upload-delete' => [
                'class' => 'trntv\filekit\actions\DeleteAction'
            ],
            'upload-imperavi' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                'multiple' => true,
                'fileStorage' => 'fileStorage', 
            ],
            'avatar-upload' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                'deleteRoute' => 'avatar-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    $img = \Intervention\Image\ImageManagerStatic::make($file->read())->fit(215, 215);
                    $file->put($img->encode());
                }
            ],
            'avatar-delete' => [
                'class' => 'trntv\filekit\actions\DeleteAction'
            ],
            'input-upload' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                'deleteRoute' => 'input-delete',
                'fileStorage' => 'inputFileStorage',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    $img = \Intervention\Image\ImageManagerStatic::make($file->read())->fit(64, 64);
                    $file->put($img->encode());
                }
            ],
            'input-delete' => [
                'class' => 'trntv\filekit\actions\DeleteAction'
            ],
            'module-upload' => [
                'class' => 'trntv\filekit\actions\UploadAction',
                'deleteRoute' => 'module-delete',
                'fileStorage' => 'moduleFileStorage', 
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    $img = \Intervention\Image\ImageManagerStatic::make($file->read())->fit(115, 115);
                    $file->put($img->encode());
                }
            ],
            'module-delete' => [
                'class' => 'trntv\filekit\actions\DeleteAction'
            ]        
        ];
    }

    /**
     * Lists all FileStorageItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FileStorageItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = [
            'defaultOrder'=>['created_at'=>SORT_DESC]
        ];
        $components = \yii\helpers\ArrayHelper::map(
            FileStorageItem::find()->select('component')->distinct()->all(),
            'component',
            'component'
        );
        $totalSize = FileStorageItem::find()->sum('size') ?: 0;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'components' => $components,
            'totalSize' => $totalSize
        ]);
    }

    /**
     * Displays a single FileStorageItem model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Deletes an existing FileStorageItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FileStorageItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FileStorageItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FileStorageItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
