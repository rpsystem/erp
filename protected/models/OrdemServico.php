<?php

/**
 * This is the model class for table "ordens_servico".
 *
 * The followings are the available columns in table 'ordens_servico':
 * @property integer $id
 * @property integer $cliente_id
 * @property integer $cliente_carro_id
 * @property integer $forma_pagamento_id
 * @property string $observacao
 * @property integer $excluido
 */
class OrdemServico extends CActiveRecord {

    public $aFormasPagamento = array(
        1 => 'Dinheiro',
        2 => 'Débito',
        3 => 'Crédito',
    );

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'ordens_servico';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('cliente_id, cliente_carro_id, forma_pagamento_id, excluido', 'numerical', 'integerOnly' => true),
            array('observacao', 'safe'),
            array('cliente_id, cliente_carro_id', 'required'),
            array('id, cliente_id, cliente_carro_id, forma_pagamento_id, observacao, excluido', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        return array(
            'ordemServicoItens' => array(self::HAS_MANY, 'OrdemServicoItem', 'ordem_servico_id'),
            'cliente' => array(self::BELONGS_TO, 'Cliente', 'cliente_id'),
            'clienteCarro' => array(self::BELONGS_TO, 'ClienteCarro', 'cliente_carro_id'),
        );
    }

    public function scopes() {
        return array(
            'naoExcluido' => array(
                'condition' => 't.excluido = false'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'Número',
            'cliente_id' => 'Cliente',
            'cliente_carro_id' => 'Placa do carro',
            'forma_pagamento_id' => 'Forma de pagamento',
            'observacao' => 'Observação',
            'excluido' => 'Excluido',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('cliente_id', $this->cliente_id);
        $criteria->compare('cliente_carro_id', $this->cliente_carro_id);
        $criteria->compare('forma_pagamento_id', $this->forma_pagamento_id);
        $criteria->compare('observacao', $this->observacao, true);
        $criteria->compare('excluido', $this->excluido);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return OrdemServico the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getValorTotal() {
        $valor_total = 0;
        if (!empty($this->ordemServicoItens)) {
            foreach ($this->ordemServicoItens as $item) {
                if ($item->item_id != 0) {
                    if ($item->tipo_item_id == 1) {
                        $valor_total = $valor_total + $item->produto->preco;
                    }
                    if ($item->tipo_item_id == 2) {
                        $valor_total = $valor_total + $item->servico->preco;
                    }
                } else {
                    $oLogItemNaoCadastrado = LogItemNaoCadastrado::model()->findByAttributes(array(
                        'ordem_servico_item_id' => $item->id,
                    ));
                    $valor_total = $valor_total + $oLogItemNaoCadastrado->preco;
                }
            }
        }
        return $valor_total;
    }

    public function finalizarOS() {
        if (!empty($_POST['OrdemServicoTipoPagamento'])) {
            $oLogOrdemServico = new LogOrdemServico;
            $oLogOrdemServico->status = 2;
            $oLogOrdemServico->ordem_servico_id = $this->id;
            $oLogOrdemServico->observacao = $_POST['LogOrdemServico']['observacao'];
            if ($oLogOrdemServico->salvarLog()) {
                foreach ($_POST['OrdemServicoTipoPagamento'] as $post) {
                    if (!empty($post['forma_pagamento_id'])) {
                        $oOrdemServicoTipPagamento = new OrdemServicoTipoPagamento;
                        $oOrdemServicoTipPagamento->ordem_servico_id = $this->id;
                        $oOrdemServicoTipPagamento->forma_pagamento_id = $post['forma_pagamento_id'];
                        $oOrdemServicoTipPagamento->valor = $post['valor'];
                        $oOrdemServicoTipPagamento->parcelas = $post['parcelas'];
                        $oOrdemServicoTipPagamento->save();
                    }
                }
                return true;
            }
        }
        return false;
    }

}
