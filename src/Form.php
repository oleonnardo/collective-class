<?php

namespace Collective\Helpers;

use Collective\Helpers\Builders\Builder;
use Collective\Helpers\Html;

class Form extends Builder{

    /**
     * Define um pré modelo de dados ao formulário
     * @var
     */
    private static $model;

    /**
     * Métodos que o <form> permite receber
     * @var array
     */
    private static $methods = ['get', 'post', 'delete', 'patch', 'GET', 'POST', 'DELETE', 'PATCH'];


    /**
     * Modelo boostrap de form VERTICAL
     * @var string
     */
    private static $formVertical = '
        <div class="form-group">
            %label%  %input%
         </div>';

    /**
     * Modelo boostrap de form HORIZONTAL
     * @var string
     */
    private static $formHorizontal = '
        <div class="form-group row">
            %label%
            <div class="%row%">%input%</div>
        </div>';

    /**
     * Modelo de formulário padrão a ser criado
     * @var null
     */
    private static $typeForm = 'horizontal';

    /**
     * O model() irá preencher um formulário com base no conteúdo de um modelo.
     * @param $model
     * @param $route
     * @param array $attributes
     * @return string
     */
    public static function model($model, $route, $attributes = array()){
        self::$model = $model;

        // Abrimos um formulário
        return self::open($route, $attributes);
    }

    /**
     * Abrindo um formulário
     * @param $route
     * @param array $attributes
     * @return string
     */
    public static function open($route, $attributes = array()){

        // Definição da action
        $attributes['action'] = $route;

        /**
         * Parâmetros de Atalho:
         * - array(on, off) => o usuário poderá ativar ou não o atributo "autocomplete" no formulário
         * - files => o usuário pode habilitar a permissão de submissão de arquivos pelo form
         * - method => o usuário pode definir o tipo de method do form, sem precisar adicionar o atributo ao array "attributes"
         */
        $attributes = self::addAttr('autocomplete', ['on', 'off'], $attributes);
        $attributes = self::addAttr('enctype', ['files'], $attributes, 'multipart/form-data');
        $attributes = self::addAttr('method', self::$methods, $attributes);

        return Html::decode( '<form ' . self::attributes($attributes) . '>' );
    }

    /**
     * Fechando um formulário
     * @return string
     */
    public static function close(){
        return Html::decode( '</form>' );
    }

    /**
     * Cria um formulário no estilo "vertical", com base no bootstrap.
     * @param $model
     * @param $route
     * @param array $attributes
     * @param array $inputs
     * @return string
     */
    public static function vertical($model, $route, $attributes = array(), $inputs = array()){
        self::$typeForm = 'vertical';
        return self::create(self::$formVertical, $model, $route, $attributes, $inputs);
    }

    /**
     * Cria um formulário no estilo "horizontal", com base no bootstrap.
     * @param $model
     * @param $route
     * @param array $attributes
     * @param array $inputs
     * @return string
     */
    public static function horizontal($model, $route, $attributes = array(), $inputs = array()){
        self::$typeForm = 'horizontal';
        return self::create(self::$formHorizontal, $model, $route, $attributes, $inputs);
    }

    /**
     * Responsável por montar um formulário baseado no BOOTSTRAP.
     * A função create(), será chamada nas funções vertical() e horizontal()
     * @param $elementForm
     * @param $model
     * @param $route
     * @param array $attributes
     * @param array $inputs
     * @return string
     */
    private static function create($elementForm, $model, $route, $attributes = array(), $inputs = array()){

        // Abrindo o formulário <form>
        $form = self::model($model, $route, $attributes);

        // Será lido o array de inputs que foi formatado pelo usuário
        foreach ($inputs as $name => $parameters) {

            /**
             * A função createLabel() irá montar o elemento label com base no modelo que foi definido pelo usuário.
             * <label for="{for}" {attributeLabel}>
             */
            $html = self::createLabel($elementForm, $name, $parameters);

            /**
             * A função createInput() irá montar o elemento input com base no modelo que foi definido pelo usuário.
             * <input type="{type}" {attributeInput}>
             */
            $html = self::createInput($html, $name, $parameters);

            $form .= $html;
        }


        // Fechando o formulário </form>
        $form .= self::close();

        return Html::decode($form);
    }

    /**
     * Responsavel por criar dinâmicamente, elementos LABEL.
     * Função será chamada através do create().
     * @param $elementForm
     * @param $for
     * @param array $input
     * @return mixed
     */
    private static function createLabel($elementForm, $for, $input = array()){

        // definição do nome do Label
        $name = isset($input[2]) ? $input[2] : '(none)';

        // verifica se é realmente um array de atributos que está sendo passado para a leitura
        $attrLabel = isset($input[3]) && is_array($input[3]) ? $input[3] : array();

        return str_replace('%label%', self::label($for, $name, $attrLabel), $elementForm);
    }

    /**
     * Responsavel por criar dinâmicamente, elementos LABEL.
     * Função será chamada através do create().
     * @param $elementForm
     * @param $name
     * @param array $input
     * @return string
     */
    private static function createInput($elementForm, $name, $input = array()){
        $row = 'col-sm-6';

        // definição do tipo de "input" que serã criado
        $type = isset($input[0]) ? $input[0] : 'text';

        if( self::$typeForm === 'horizontal' ){
            $row = isset($input['row']) ? $input['row'] : 'col-sm-10';
            unset($input[ array_search('row', $input) ]);
        }

        // verifica se é realmente um array de atributos que está sendo passado para a leitura
        $attrInput = isset($input[1]) && is_array($input[1]) ? $input[1] : array();

        $html = str_replace('%row%', $row, $elementForm);
        $html = str_replace('%input%', self::input($type, $name, null, $attrInput), $html);

        return $html;
    }

    /**
     * Gerando um elemento label ao formulário
     * @param $for
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function label($for, $value, $attributes = array()){
        $attributes['for'] = $for;
        return self::tag('label', $value, self::attributes($attributes));
    }

    /**
     * Proteção CSRF
     * Se você usar o método open() ou model() com POST, PUT ou DELETE, o token CSRF usado para
     * a validação das ações. Será adicionado ao formulário um campo oculto (hidden) automaticamente.
     * @return string
     */
    public static function token(){
        return self::hidden('_token', bin2hex(openssl_random_pseudo_bytes(32)));
    }

    /**
     * Gerando um campo do tipo "button"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function button($name, $value, $attributes = array()){
        return self::input('button', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "reset"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function reset($name, $value, $attributes = array()){
        return self::input('reset', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "submit"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function submit($name, $value, $attributes = array()){
        return self::input('submit', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "text"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function text($name, $value, $attributes = array()){
        return self::input('text', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "password"
     * @param $name
     * @param array $attributes
     * @return string
     */
    public static function password($name, $attributes = array()){
        return self::input('password', $name, null, $attributes);
    }

    /**
     * Gerando um campo do tipo "hidden" - oculto
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function hidden($name, $value, $attributes = array()){
        $attributes[] = 'readonly';
        return self::input('hidden', $name, $value, $attributes);
    }

    /**
     * É possivel gerar vários campos do tipo "hidden" de uma só vez!
     * @param array $options
     * @param array $attributes
     * @return string
     */
    public static function hidden_multiple($options = array()){
        $hidden_inputs = '';

        /**
         * O array options contém todos os campos que serão criados como tipo "hidden" (ocultos)
         */
        foreach ($options as $key => $item) {
            $hidden_inputs .= self::hidden($key, $item, array('id'));
        }

        return $hidden_inputs;
    }

    /**
     * Gerando um campo do tipo "email"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function email($name, $value, $attributes = array()){
        return self::input('email', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "file"
     * @param $name
     * @param array $attributes
     * @return string
     */
    public static function file($name, $attributes = array()){
        return self::input('file', $name, null, $attributes);
    }

    /**
     * Gerando um campo do tipo "search"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function search($name, $value, $attributes = array()){
        return self::input('search', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "url"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function url($name, $value, $attributes = array()){
        return self::input('url', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "number"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function number($name, $value, $attributes = array()){
        return self::input('number', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "tel"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function tel($name, $value, $attributes = array()){
        return self::input('tel', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "date"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function date($name, $value, $attributes = array()){
        return self::input('date', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "datetime-local"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function datetime_local($name, $value, $attributes = array()){
        return self::input('datetime-local', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "month"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function month($name, $value, $attributes = array()){
        return self::input('month', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "week"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function week($name, $value, $attributes = array()){
        return self::input('week', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "time"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function time($name, $value, $attributes = array()){
        return self::input('time', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "range"
     * @param $name
     * @param $min
     * @param $max
     * @param null $value
     * @param array $attributes
     * @return string
     */
    public static function range($name, $min, $max, $value = null, $attributes = array()){
        $attributes['min'] = (string)$min;
        $attributes['max'] = (string)$max;
        return self::input('range', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "color"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function color($name, $value, $attributes = array()){
        return self::input('color', $name, $value, $attributes);
    }

    /**
     * Gerando um campo do tipo "radio"
     * @param $name
     * @param $value
     * @param bool $checked
     * @param array $attributes
     * @return string
     */
    public static function radio($name, $value, $checked = false, $attributes = array()){
        $attributes[] = ( $checked === true ) ? 'checked' : '';
        return self::input('radio', $name, $value, $attributes);
    }

    /**
     * É possivel gerar vários campos do tipo "radio" de uma só vez!
     * @param $name
     * @param array $options
     * @param null $checked
     * @param array $attributes
     * @return string
     */
    public static function radio_multiple($name, $options = array(), $checked = null, $attributes = array()){
        $radio = '';
        $attr = array();

        $count = 0;
        foreach ($options as $key => $item) {

            /**
             * A função radioAndCheckAttributes() irá capturar os
             * atributos individuais de cada botão de rádio que será criado
             */
            $attr = self::radioAndCheckAttributes($key, $attributes);

            // A função hasChecked(), irá verificar quais botões de rádio estarão marcados
            $check = self::hasChecked($checked, $key);

            /**
             * As próximas duas linhas irão montar o elemento do botão de rádio:
             * Saida:
             * <label for="{value}">
             *      <input type="radio" {$attributes}> {$nameLabel}
             * </label>
             */
            $radio_button = self::radio($name, $item, $check, array('id' => $key)) . ' ' . $item;
            $radio .= ' ' . self::label($key, $radio_button, $attr) . '<br>';

            $attr = array();
            $count++;
        }

        return $radio;
    }

    /**
     * Gerando um campo do tipo "checkbox"
     * @param $name
     * @param $value
     * @param bool $checked
     * @param array $attributes
     * @return string
     */
    public static function checkbox($name, $value, $checked = false, $attributes = array()){
        $attributes[] = ( $checked === true ) ? 'checked' : '';
        return self::input('checkbox', $name, $value, $attributes);
    }

    /**
     * É possivel gerar vários campos do tipo "checkbox" de uma só vez!
     * @param $name
     * @param array $options
     * @param null $checked
     * @param array $attributes
     * @return string
     */
    public static function checkbox_multiple($name, $options = array(), $checked = null, $attributes = array()){
        $checkbox = '';
        $attr = array();

        $count = 0;
        foreach ($options as $key => $item) {

            /**
             * A função radioAndCheckAttributes() irá capturar os
             * atributos individuais de cada botão de rádio que será criado
             */
            $attr = self::radioAndCheckAttributes($key, $attributes);

            // A função hasChecked(), irá verificar quais botões de radio estarão marcados
            $check = self::hasChecked($checked, $key);

            /**
             * As próximas duas linhas irão montar o elemento do checkbox:
             * Saida:
             * <label for="{value}">
             *      <input type="checkbox" {$attributes}> {$nameLabel}
             * </label>
             */
            $checkbox_button = self::checkbox($name, $item, $check, array('id' => $key)) . ' ' . $item;
            $checkbox .= ' ' . self::label($key, $checkbox_button, $attr) . '<br>';

            $attr = array();
            $count++;
        }

        return $checkbox;
    }

    /**
     * Responsável por capturar os atributos individuais de cada botão de rádio/checkbox que será criado
     * @param $key
     * @param $attributes
     * @return array
     */
    private static function radioAndCheckAttributes($key, $attributes){
        $mixed = array();

        /**
         * Primeiramente será verifica se existe um array de configurações dentro do array de atributos que foi pré definido no radio/checkbox;
         *
         * Após isso, o array com os parâmetros de configurações será lido e criado um novo array ($mixed) com os parâmetros de configurações
         * daquele botão de radio/checkbox que está sendo lido no momento.
         */
        if( isset($attributes[$key]) ){
            foreach ($attributes[$key] as $index => $attribute) {
                $mixed[$index] = $attribute;
            }
        }

        return $mixed;
    }

    /**
     * Verifica se um item RADIO/CHECKBOX está SELECIONADO.
     * Função será utilizada nas chamadas das funções radio_multiple() e checkbox_multiple()
     * @param $itemChecked
     * @param $key
     * @return bool
     */
    private static function hasChecked($itemChecked, $key){

        // Caso a chamada da função seja um checkbox, é possivel marcar mais de uma caixa, então esse if se torna TRUE
        if( is_array($itemChecked) ){
            return in_array($key, $itemChecked);
        }

        return $itemChecked == $key;
    }

    /**
     * Adiciona um elemento input ao formuálrio
     * @param $type
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    private static function input($type, $name, $value, $attributes = array()){

        /**
         * A função hasValue() irá fazer alguns testes do elemento input ser totalmente criado.
         * Mas no fim, ele vai indicar se o input terá o atributo "value" com um valor já pré definido
         * na chamada do 'input()' ou um valor que foi definido em um modelo (model).
         */
        $value = self::hasValue($name, $value, $type);

        /**
         * A função addAttr(), é um função de atalho.
         * Será verificado se um determinado parâmetro foi definido no array attributes(), caso positivo
         * esse atributo será adicionado como atributo ao input
         */
        $attributes = self::addAttr('id', ['id'], $attributes, $name);

        /**
         * O array de atributos será convertido em string
         * Entrada:
         * $attributes = array(
         *     'key' => 'value'
         * );
         *
         * Saída:
         * {key} = "{value}"
         */
        $attributes = self::attributes($attributes);

        return Html::decode( '<input name="' . $name . '" type="' . $type . '" ' . $value . ' ' . $attributes . '>' );
    }

    /**
     * Gerando um elemento html "textarea"
     * @param $name
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function textarea($name, $value, $attributes = array()){

        /**
         * O array de atributos será convertido em string
         * Entrada:
         * $attributes = array(
         *     'key' => 'value'
         * );
         *
         * Saída:
         * {key} = "{value}"
         */
        $attributes = self::attributes($attributes);

        /**
         * A função hasValue() irá fazer alguns testes do elemento input ser totalmente criado.
         * Mas no fim, ele vai indicar se o input terá o atributo "value" com um valor já pré definido
         * na chamada do 'input()' ou um valor que foi definido em um modelo (model).
         */
        $value = self::hasValue($name, $value, 'textarea');

        return Html::decode( self::tag('textarea', $value, $attributes) );
    }

    /**
     * Gerando uma caixa de seleção (SELECT)
     * @param $name
     * @param array $options
     * @param null $selected
     * @param array $attributes
     * @param array $style
     * @return string
     */
    public static function select($name, $options = array(), $selected = null, $attributes = array(), $style = array()){

        /**
         * A função addAttr(), é um função de atalho.
         * Será verificado se um determinado parâmetro foi definido no array attributes(), caso positivo
         * esse atributo será adicionado como atributo ao input
         */
        $attributes = self::addAttr('id', ['id'], $attributes, $name);

        $attributes['name'] = $name;

        /**
         * Abrindo a caixa de seleção <select>
         * O array de atributos será convertido em string
         *
         * Entrada:
         * $attributes = array(
         *     'key' => 'value'
         * );
         *
         * Saída:
         * {key} = "{value}"
         */
        $select = '<select ' . self::attributes($attributes) . '>';

        // A função addChildSelect(), irá adicionar os itens (OPTION) à caixa de seleção (SELECT)
        $select .= self::addChildSelect($options, $selected, $attributes, $style);

        // Fechando a caixa de seleção </select>
        $select .= '</select>';

        return Html::decode($select);
    }

    /**
     * Responsável por adicionar OPTIONS e OPTGROUPS à uma caixa de seleção (SELECT)
     * @param array $options
     * @param null $selected
     * @param array $attributes
     * @param array $style
     * @return string
     */
    private static function addChildSelect($options = array(), $selected = null, $attributes = array(), $style = array()){
        $childSelect = '';

        /**
         * O array options() possui os valores que serão adicionados aos options do select.
         *
         * Entrada:
         * $options = array(
         *      'key' => 'value'
         * );
         * Saída:
         * <option value="{key}">{value}</option>
         */
        foreach ($options as $key => $item) {

            /**
             * Caso o item {value} seja um array, o loop irá adicionar um OPTGROUP, através da função optgroup(),
             * caso contrário, será adicionado um OPTION à caixa de seleção (SELECT)
             */
            $childSelect .= is_array($item) ?
                self::optgroup($key, $item, $selected, $attributes, $style) :
                self::option($key, $item, $selected, $attributes, $style);

        }

        return $childSelect;
    }

    /**
     * Responsável por montar um <optgroup></optgroup> à caixa de seleção (SELECT)
     * @param $label
     * @param array $options
     * @param null $selected
     * @param array $attributes
     * @param array $style
     * @return string
     */
    private static function optgroup($label, $options = array(), $selected = null, $attributes = array(), $style = array()){

        // Abrindo a tag "optgroup"
        $optgroup = '<optgroup label="' . $label . '">';

        // Lendo o array optgroup() e adicionando "options"
        foreach ($options as $key => $item) {
            $optgroup .= self::option($key, $item, $selected, $attributes, $style);
        }

        // Fechando a tag "optgroup"
        $optgroup .= '</optgroup>';

        return $optgroup;
    }

    /**
     * Responsável por montar um <option></option> à caixa de seleção (SELECT)
     * @param $value
     * @param $name
     * @param null $selected
     * @param array $attributes
     * @param array $style
     * @return string
     */
    private static function option($value, $name, $selected = null, $attributes = array(), $style = array()){

        // A função hasSelected(), irá verificar qual OPTION está selecionado
        $attributes[] = self::hasSelected($attributes['name'], $value, $selected);

        $attributes['value'] = $value;

        /**
         * Abrindo a caixa de seleção <select>
         * O array de atributos será convertido em string
         *
         * Entrada:
         * $attributes = array(
         *     'key' => 'value'
         * );
         *
         * Saída:
         * {key} = "{value}"
         */
        $attr = self::attributes($attributes);

        /**
         * O array style, possui parâmetro de configurações individuais para cada OPTION.
         * No if() a seguir, ele verifica se existe um parâmetro para o {value/option} atual.
         *
         * Caso seja verdadeiro, as configurações serão convertidas para string através da função "attributes()"
         */
        if( isset($style[$value]) ){
            $attr .= ' ' . self::attributes($style[$value]);
        }

        return Html::decode( self::tag('option', $name, $attr) );
    }

    /**
     * Verifica se um item do OPTION está selecionado.
     * Função será utilizada na chamada da função option()
     * @param $name
     * @param $item
     * @param $selected
     * @return string
     */
    private static function hasSelected($name, $item, $selected){

        // Verifica se o item está setado em algum modelo pré definido na abertura do formulário
        if( isset(self::$model[$name]) ){
            $selected = self::$model[$name];
        }

        return $item == $selected ? 'selected' : '';
    }

    /**
     * Responsável por setar/adicionar valores nos inputs e elementos que serão utilizados no formulário.
     * Esses valores podem partir de um modelo pré definido ou não.
     * @param $name
     * @param $value
     * @param string $type
     * @return string
     */
    private static function hasValue($name, $value, $type = ''){

        /**
         * Caso o hasValue() esteja sendo chamado a partir das funções de radio() ou checkbox()
         * Por serem elementos que para serem setados necessitam de um valor BOOLEAN
         */
        if( $type === 'radio' || $type === 'checkbox' ){

            // verifica se o item já está pré definido em um modelo
            if(isset(self::$model[$name])){
                return $value == self::$model[$name] ? 'checked' : '';
            }
            return '';
            // o hasValue() não será mais executado para os elementos RADIO e CHECKBOX
        }

        /**
         * Verifica se o {value} possui algum valor definido na chamada de algum método.
         * Caso positivo o valor irá permanecer, e NÃO será substituído.
         */
        $val = empty($value) ? '': $value;

        // verifica se o item já está pré definido em um modelo
        if(isset(self::$model[$name])){
            $val .= self::$model[$name];
        }

        // Caso o hasValue() esteja sendo chamado a partir da função textarea()
        if( $type === 'textarea' ){
            return $val;
            // o hasValue() não será mais executado para o elemento TEXTAREA
        }

        // Para os demais elementos o atributo "value" será adicionado.
        return empty($val) ? '' : 'value="' . $val . '"';
    }

}