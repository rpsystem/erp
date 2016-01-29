<?php

class OrdemServicoHelper {

    public static function getHtml($obj, $remover = true) {
        $return = '';
        $return .= '<tr identificador="' . $obj->tipoItem . '_' . $obj->identificador . '">';
        $return .= '<td>';
        $return .= $obj->titulo;
        $return .= '</td>';
        $return .= '<td>';
        $return .= 'R$' . FormatHelper::valorMonetario($obj->preco);
        $return .= '</td>';
        if ($remover) {
            $return .= '<td>';
            $return .= '<a href="javascript:void(0)" class="remove" onclick="removerItem(' . $obj->tipoItem . ', ' . $obj->item_id . ', ' . $obj->identificador . ', ' . $obj->preco . ')">';
            $return .= '<i class="fa fa-times"></i>';
            $return .= '</a>';
            $return .= '</td>';
        }
        $return .= '</tr>';
        return $return;
    }

    public static function renderItens($tipoItem, $oOrdemServicoItens, $remover = true) {
        $return = '';
        if (!empty($oOrdemServicoItens)) {
            $identificador = 1400;
            foreach ($oOrdemServicoItens as $model) {
                $oLogItemNaoCadastrado = LogItemNaoCadastrado::model()->findByAttributes(array(
                    'ordem_servico_item_id' => $model->id,
                ));
                $obj = new stdClass();
                $obj->tipoItem = $tipoItem;
                $obj->identificador = $identificador;
                if ($tipoItem == 1 && $model->tipo_item_id == 1) {
                    if ($model->item_id != 0) {
                        $obj->titulo = $model->produto->titulo;
                        $obj->preco = !empty($model->preco) ? $model->preco : $model->produto->preco;
                        $obj->item_id = $model->item_id;
                        $return .= self::getHtml($obj, $remover);
                    } else {
                        $obj->titulo = $oLogItemNaoCadastrado->titulo;
                        $obj->preco = $oLogItemNaoCadastrado->preco;
                        $obj->item_id = $identificador;
                        $return .= self::getHtml($obj, $remover);
                    }
                }
                if ($tipoItem == 2 && $model->tipo_item_id == 2) {
                    if ($model->item_id != 0) {
                        $obj->titulo = $model->servico->titulo;
                        $obj->preco = !empty($model->preco) ? $model->preco : $model->servico->preco;
                        $obj->item_id = $model->item_id;
                        $return .= self::getHtml($obj, $remover);
                    } else {
                        $obj->titulo = $oLogItemNaoCadastrado->titulo;
                        $obj->preco = $oLogItemNaoCadastrado->preco;
                        $obj->item_id = $identificador;
                        $return .= self::getHtml($obj, $remover);
                    }
                }
                $identificador++;
            }
        }
        if (empty($return)) {
            $colspan = $remover ? 3 : 2;
            $return = '<td colspan="' . $colspan . '" class="sem_item">Não há itens cadastrados nesta sessão.';
        }
        return $return;
    }

    public static function renderLogs($oLogsOrdemServico) {
        $return = '';

        foreach ($oLogsOrdemServico as $log) {
            $return .= '<tr>';
            $return .= '<td>';
            $return .= $log->aStatus[$log->status];
            $return .= '</td>';
            $return .= '<td>';
            $return .= RPFormat::dataHora($log->data_hora);
            $return .= '</td>';
            $return .= '<td>';
            $return .= $log->usuario->nome;
            $return .= '</td>';
            $return .= '</tr>';
        }

        return $return;
    }

    public static function getHtmlAbrirOS($tipoItem, $obj) {
        $return = '';
        $return .= '<tr>';
        $return .= '<td style="width: 50px">';
        $return .= '<input ' . $obj->checked . ' class="selecionaItem" preco_variavel="' . $obj->preco_variavel . '" valor="' . $obj->preco . '" tipo_item="' . $tipoItem . '" item_id="' . $obj->id . '" type="checkbox" value="' . $obj->id . '" name="OrdemServicoItem[Item][' . $tipoItem . '][' . $obj->id . '][id]">';
        $return .= '</td>';
        $return .= '<td class="titulo item_' . $tipoItem . '_' . $obj->id . '">' . $obj->titulo;
        $return .= '</td>';
        $return .= '<td>';
        $return .= '<input class="preco item_' . $tipoItem . '_' . $obj->id . '" disabled="disabled" type="text" value="' . $obj->preco . '" name="OrdemServicoItem[Item][' . $tipoItem . '][' . $obj->id . '][preco]">';
        $return .= '</td>';
        $return .= '</tr>';
        return $return;
    }

    public static function renderItensOS($tipoItem, $oItens, $oOrdemServicoItens) {
        $return = '';
        $obj = new stdClass();

        $aItens = array();
        foreach ($oOrdemServicoItens as $item) {
            if ($tipoItem == $item->tipo_item_id) {
                $aItens['ids'][] = $item->item_id;
                $aItens[$item->item_id]['preco'] = $item->preco;
            }
        }

//        echo '<pre>';
//        die(var_dump($aItens));

        foreach ($oItens as $item) {
            $obj->id = $item->id;
            $obj->preco_variavel = $item->preco_variavel;
            $obj->preco = !empty($aItens[$item->id]['preco']) ? $aItens[$item->id]['preco'] : $item->preco;
            $obj->titulo = $item->titulo;
            $obj->checked = in_array($item->id, $aItens['ids']) ? 'checked' : '';
            $return .= self::getHtmlAbrirOS($tipoItem, $obj);
        }

        return $return;
    }

}
