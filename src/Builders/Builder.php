<?php

namespace Collective\Helpers\Builders;

abstract class Builder {

    protected static function attributes($attributes = array()){
        $html = '';

        foreach ($attributes as $key => $value){
            $html .= is_numeric($key) ? $value . ' ' : $key . '="' . $value . '" ';
        }

        return substr($html, 0, strlen($html)-1);
    }

    protected static function tag($tag, $value, $attributes = ''){
        return '<' . $tag . ' ' . $attributes . '>' . $value . '</' . $tag . '>';
    }

    protected static function hasKey($attributes = array(), $indice, $value){
        return array_key_exists($indice, $attributes) ? $attributes[$indice] : $value;
    }

    protected static function addAttr($attr, $options = array(), $attributes = array()){
        foreach ($options as $name) {
            if (in_array($name, $attributes)) {
                $attributes[$attr] = $name;
                unset($attributes[ array_search($name, $attributes) ]);
                return $attributes;
            }
        }
        return $attributes;
    }

    protected static function transformAttr($key, $tag, $value, $mixed = array()){
        if( in_array($key, $mixed) ){
            $mixed[$tag] = $value;
            unset($mixed[ array_search($key, $mixed) ]);
        }
        return $mixed;
    }

    protected static function addAttrOfArray($index, $mixed){
        return (isset($mixed[$index])) ? $mixed[$index] : null;
    }

}