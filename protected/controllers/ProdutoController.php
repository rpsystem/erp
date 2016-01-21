<?php

class ProdutoController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl',
            'postOnly + delete',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('index', 'view'),
                'users' => array('*'),
            ),
            array('allow',
                'actions' => array('create', 'update', 'estoque', 'admin', 'delete'),
                'users' => array('@'),
            ),
            array('allow',
                'actions' => array(),
                'users' => array('admin'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new Produto;

        $oModelos = Modelo::model()->ordenarTitulo()->naoExcluido()->findAll();
        $oMarcas = Marca::model()->ordenarTitulo()->naoExcluido()->findAll();
        $oTiposProduto = TipoProduto::model()->ordenarTitulo()->naoExcluido()->findAll();
                
        if (isset($_POST['Produto'])) {
            $model->attributes = $_POST['Produto'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('create', array(
            'model' => $model,
            'oModelos' => $oModelos,
            'oMarcas' => $oMarcas,
            'oTiposProduto' => $oTiposProduto,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);

        $oModelos = Modelo::model()->ordenarTitulo()->naoExcluido()->findAll();
        $oMarcas = Marca::model()->ordenarTitulo()->naoExcluido()->findAll();
        $oTiposProduto = TipoProduto::model()->ordenarTitulo()->naoExcluido()->findAll();

        if (isset($_POST['Produto'])) {
            $model->attributes = $_POST['Produto'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('update', array(
            'model' => $model,
            'oModelos' => $oModelos,
            'oMarcas' => $oMarcas,
            'oTiposProduto' => $oTiposProduto,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();

        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $dataProvider = new CActiveDataProvider('Produto');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin() {
        $model = new Produto('search');
        $model->unsetAttributes();
        
        $oTiposProdutos = TipoProduto::model()->ordenarTitulo()->findAll(array(
            'condition' => 'id in ('.implode(",", CHtml::listData(Produto::model()->findAll(), 'tipo_produto_id', 'tipo_produto_id')).')',
        ));
        
        if (isset($_GET['Produto']))
            $model->attributes = $_GET['Produto'];

        $this->render('admin', array(
            'model' => $model,
            'oTiposProdutos' => $oTiposProdutos,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Produto the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = Produto::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param Produto $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'produto-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
    
    public function actionEstoque(){
        
    }

}
