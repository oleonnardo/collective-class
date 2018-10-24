<?php

/**
 * ---------------- [ Class Table ] ----------------
 *
 * A tag <table> define uma tabela HTML.
 *
 * Uma tabela HTML consiste no elemento <table> e em um ou mais elementos <tr>, <th> e <td>.
 * O elemento <tr> define uma linha da tabela, o elemento <th> define um cabeçalho da tabela e o elemento <td> define uma célula da tabela.
 *
 * Uma tabela HTML mais complexa também pode incluir <caption>, <col>, <colgroup>, <thead>, <tfoot>, e elementos <tbody>
 *
 * Os navegadores podem usar esses elementos para ativar a rolagem do corpo da tabela, independentemente do cabeçalho e rodapé.
 * Além disso, ao imprimir uma tabela grande que abrange várias páginas, esses elementos podem permitir que o cabeçalho
 * e o rodapé da tabela sejam impressos na parte superior e inferior de cada página.
 */

namespace Collective\Helpers;

use Collective\Helpers\Builders\Builder;
use Collective\Helpers\Html;

class Table extends Builder {

    /**
     *
     * @var
     */
    private static $model;

    private static $columnsOfTable;

    private static $attributesColumnsOfTable;

    /**
     * Define uma mensagem padrão caso nenhum registro seja adicionado à tabela
     * @var
     */
    private static $message = 'Nenhum dado a ser exibido encontrado.';

    private static $rowspan = 0;

    private static $colspan = 0;

    /**
     * Especifica o alinhamento de uma tabela de acordo com o texto ao redor
     * @var array
     */
    private static $alignTable = ['right', 'left', 'center'];

    private static $alignPartOf = ['right', 'left', 'center', 'justify', 'char'];

    private static $alignValing = ['top', 'middle', 'bottom', 'baseline'];

    /**
     * Cria uma tabela já preenchida com dados de um array ou de uma variável qualquer
     * O item $settings, irá receber quais campos (indices) que estão inclusas na varável $model
     *
     * @param $model
     * @param array $settings
     * @param array $attributes
     * @return string
     */
    public static function model($model, $settings = array(), $attributes = array()){
        self::$model = $model;

        $new_fetch = array();
        $cont = 0;
        foreach (self::$model as $item) { // percorrendo o array com as informações da tabela
            foreach ($settings as $key){ // percorrendo o array com as possívels chaves do array $model
                if( isset($item[$key]) ){
                    // Caso o exista o índice no array, ele será transferido para um novo array
                    $new_fetch[$cont][] = $item[$key];
                }
            }
            $cont++;
        }

        // o array $model é atualizado
        self::$model = $new_fetch;

        return self::open(self::$model, $attributes);
    }

    public static function open($model, $attributes = array()){
        $attr = '';

        self::$model = $model;

        self::$rowspan = count(self::$model);

        $attributes['width'] = self::hasKey($attributes, 'width', '100%');
        $attributes['border'] = self::hasKey($attributes, 'border', '0');

        $attributes = self::addAttr('align', self::$alignTable, $attributes);
        $attributes = self::addAttr('align', self::$alignTable, $attributes);

        $attr = self::attributes($attributes);

        return Html::decode('<table ' . $attr . '>');
    }

    public static function close(){
        return Html::decode('</table>');
    }

    /**
     * Altera a string da mensagem padrão, de quando não houver nenhum registro na tabela.
     * @param $message
     */
    public static function setMessage($message){
        self::$message = $message;
    }

    /**
     * A tag <thead> é usada para agrupar o conteúdo do cabeçalho em uma tabela HTML.
     *
     * O elemento <thead> é usado em conjunto com os elementos <tbody> e <tfoot>
     * para especificar cada parte de uma tabela (cabeçalho, corpo, rodapé).
     *
     * A tag <thead> deve ser usada no seguinte contexto: Como um filho de um elemento <table>,
     * depois de qualquer elemento <caption> e <colgroup>, e antes de qualquer <tbody>, <tfoot> e <tr> elementos.
     *
     * @param array $columns
     * @param array $attributes
     * @return string
     */
    public static function thead($columns = array(), $attributes = array()){
        self::$columnsOfTable = $columns;
        self::$attributesColumnsOfTable = $attributes;
        return Html::decode( self::partsOfTable('thead', 'th', $attributes) );
    }

    /**
     * A tag <tbody> é usada para agrupar o conteúdo do corpo em uma tabela HTML.
     *
     * O elemento <tbody> é usado em conjunto com os elementos <thead> e <tfoot>
     * para especificar cada parte de uma tabela (corpo, cabeçalho, rodapé).
     *
     * A tag <tbody> deve ser usada no seguinte contexto: Como um filho de um elemento <table>,
     * depois de qualquer elemento <caption>, <colgroup> e <thead>.
     *
     * @param array $attributes
     * @return string
     */
    public static function tbody($attributes = array()){
        self::$columnsOfTable = self::$model;

        if( empty(self::$columnsOfTable) ){
            return '<tr><td colspan="' . self::$colspan . '" align="center">' . self::$message . '</td></tr>';
        }

        $tr_td = '';
        $attr = self::catchAttributesTd($attributes);

        foreach (self::$columnsOfTable as $column){

            $tr_td .= '<tr>';

            foreach ($column as $key => $value){

                $continuousAttr = '';
                $tr_td .= '<td ' . $attr['all'];

                if(! empty($attr['td']) ){
                    $continuousAttr = (isset($attr['td'][$key])) ? ' ' . $attr['td'][$key] : '';
                }

                $tr_td .= $continuousAttr . '>' . $value . '</td>';

            }

            $tr_td .= '</tr>';

        }

        return Html::decode($tr_td);
    }

    /**
     * A tag <tfoot> é usada para agrupar o conteúdo do rodapé em uma tabela HTML.
     *
     * O elemento <tfoot> é usado em conjunto com os elementos <thead> e <tbody> para especificar cada parte de uma tabela (rodapé, cabeçalho, corpo).
     *
     * A tag <tfoot> deve ser usada no seguinte contexto: Como um filho de um elemento <table>,
     * após quaisquer elementos <caption>, <colgroup>, <thead> e <tbody>.
     *
     * @param array $columns
     * @param array $attributes
     * @return string
     */
    public static function tfoot($columns = array(), $attributes = array()){
        self::$columnsOfTable = $columns;

        $attributes = self::$attributesColumnsOfTable;

        return Html::decode( self::partsOfTable('tfoot', 'th', $attributes) );
    }

    // ** tbody, tfoot,thead
    private static function partsOfTable($part, $tag, $attributes = array()){

        $attributes = self::addAttr('align', self::$alignPartOf, $attributes);
        $attributes = self::addAttr('valign', self::$alignValing, $attributes);

        $attr = self::attributes($attributes);

        self::$colspan = count(self::$columnsOfTable);

        $partOf = '<' . $part . ' ' . $attr . '>';
        $partOf .= self::childPart($tag, self::$columnsOfTable);
        $partOf .= '</' . $part . '>';

        self::$columnsOfTable = null;

        return $partOf;
    }

    // ** constução dos td's
    private static function childPart($child, $columns = array(), $attributes = array()){
        $rowTable = '<tr>';

        $attributes = self::attributes($attributes);

        foreach ($columns as $item) {
            $rowTable .= (empty($item)) ? '' : self::tag($child, $item, $attributes);
        }

        $rowTable .= '</tr>';

        return $rowTable;
    }

    private static function catchAttributesTd($attributes = array()){
        $mixed = array('all' => null, 'td' => null);

        if(isset($attributes['all'])){
            $mixed['all'] = self::attributes($attributes['all']);
        }

        if (isset($attributes['td'])){
            foreach ($attributes['td'] as $item) {
                $mixed['td'][] = self::attributes($item);
            }
        }

        return $mixed;
    }

    /**
     *
     * A tag <caption> define uma legenda da tabela.
     * A tag <caption> deve ser inserida imediatamente após a tag <table>.
     * Nota: Você pode especificar apenas uma legenda por tabela.
     *
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function caption($value, $attributes = array()){
        return Html::decode( self::tag('caption', $value, self::attributes($attributes)) );
    }

    public static function colgroup($cols = array()){
        $tag_cols = '';

        foreach ($cols as $col){
            $tag_cols .= '<col ' . self::attributes($col) . '>';
        }

        return Html::decode( self::tag('colgroup', $tag_cols) );
    }

}