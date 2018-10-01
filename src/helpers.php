<?php



if(! function_exists('e') ){

    function e($value){
        echo $value.'<br>';
    }

}


if(! function_exists('str_clean') ){

    function str_clean($str, $ui = '-'){
        $str = preg_replace('/[áàãâä]/ui', 'a', $str);
        $str = preg_replace('/[éèêë]/ui', 'e', $str);
        $str = preg_replace('/[íìîï]/ui', 'i', $str);
        $str = preg_replace('/[óòõôö]/ui', 'o', $str);
        $str = preg_replace('/[úùûü]/ui', 'u', $str);
        $str = preg_replace('/[ç]/ui', 'c', $str);
        $str = preg_replace('/[^a-z0-9]/i', $ui, $str);
        $str = preg_replace('/_+/', $ui, $str);
        return strtolower($str);
    }

}

if(! function_exists('transform_array_value') ){

    function transform_array_value($mixed){
        $new_array = array();
        foreach ($mixed as $key => $value) {
            $new_array[] = $value;
        }
        return $new_array;
    }

}