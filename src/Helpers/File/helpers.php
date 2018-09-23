<?php

use Helpers\File\File;

if( ! function_exists('file_random_name') ){

    function file_random_name(){
        return uniqid() . '_' . date('dmYHis');
    }

}

if( ! function_exists('file_copy') ){

    function file_copy($source, $destiny){
        return copy($source, $destiny);
    }

}

if( ! function_exists('file_move') ){

    function file_move($filename, $destination){
        return move_uploaded_file($filename, $destination);
    }

}

if( ! function_exists('file_rename') ){

    function file_rename($oldname, $newname){
        return rename($oldname, $newname);
    }

}

if( ! function_exists('file_delete') ){

    function file_delete($filename){
        return unlink($filename);
    }

}

if( ! function_exists('file_extension') ){

    function file_extension($inputfile){
        $extension = pathinfo( file_tpm_name($inputfile). '/' . file_name($inputfile) );
        return $extension['extension'];
    }

}

if( ! function_exists('file_name') ){

    function file_name($inputfile){
        return $_FILES[$inputfile]['name'];
    }

}

if( ! function_exists('file_tpm_name') ){

    function file_tpm_name($inputfile){
        return $_FILES[$inputfile]['tmp_name'];
    }

}

if( ! function_exists('file_type') ){

    function file_type($inputfile){
        return $_FILES[$inputfile]['type'];
    }

}

if( ! function_exists('file_size') ){

    function file_size($inputfile){
        return $_FILES[$inputfile]['size'];
    }

}
