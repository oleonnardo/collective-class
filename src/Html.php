<?php

namespace Collective\Helpers;

use Collective\Helpers\Builders\Builder;

class Html extends Builder {

    /**
     * Converter uma string HTML em entidades.
     *
     * @param string $value
     * @return string
     */
    public static function entities($value) {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Converter entidades em caracteres HTML.
     *
     * @param string $value
     * @return string
     */
    public static function decode($value){
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Cria a tag <a>
     * @param $url
     * @param $text
     * @param array $attributes
     * @return string
     */
    public static function href($url, $text, $attributes = array()){
        $attributes = self::transformAttr('blank', 'target', '_blank', $attributes);
        $attributes['href'] = $url;

        return self::decode( self::tag('a', $text, self::attributes($attributes)) );
    }

    /**
     * Exibe um elemento html com base no boostrap - alert
     * @param $message
     * @param string $type
     * @param string $style
     * @return string
     */
    public static function alert($message, $alert='info', $style='', $type='background'){
        return self::decode('<div class="alert alert-'.$alert.' '.$type.'-'.$alert.'" style="'.$style.'">'.$message.'</div>');
    }

    /**
     * Cria um elemento boostrap badge label
     * @param $message
     * @param string $color
     * @param string $sizeBadge
     * @param string $style
     * @return string
     */
    public static function badge($message, $color='primary', $sizeBadge='label-md', $style=''){
        return self::decode('<label class="label label-'.$color.' '.$sizeBadge.'" style="'.$style.'">'.$message.'</label>');
    }

    /**
     * A tag <abbr> define uma abreviação ou um acrônimo, como "Mr.", "Dec.", "ASAP", "ATM".
     *
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function abbr($value, $attributes = array()){
        return self::decode( self::tag('abbr', $value, self::attributes($attributes)) );
    }

    /**
     * A tag <address> define as informações de contato do autor/proprietário de um documento ou artigo
     *
     * @param $value
     * @param array $attributes
     * @return string
     */
    public static function address($value, $attributes = array()){
        return self::decode( self::tag('address', $value, self::attributes($attributes)) );
    }

    public static function audio($value, $sources = array(), $attributes = array()){
        $attributes[] = 'controls';
        $html = '';

        foreach ($sources as $source){
            $html .= '<source ' . self::attributes($source) . '>';
        }

        return self::decode( self::tag('audio', $html, self::attributes($attributes)) );
    }

}