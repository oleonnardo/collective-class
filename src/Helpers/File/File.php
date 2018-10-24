<?php

namespace Collective\Helpers;

class File {

    public static function upload($inputfile, $pathfile, $extensions = array(), $namefile = 'default'){

        // ** verifica se algum arquivo está sendo enviado pelo servidor
        if( empty(file_name($inputfile)) ){
            return $namefile;
        }

        // ** testa se a extensão da imagem é válida
        $file_extension = file_extension($inputfile);

        if( self::validate_extension( $file_extension, $extensions ) === false ){
            return false;
        }

        // ** definição do nome do arquivo
        $new_name = (empty($namefile)) ? file_random_name() : $namefile;
        $new_name = $new_name . '.' . $file_extension;

        // ** então o arquivo será movido para o servidor, na pasta definida
        if( file_move( file_tpm_name($inputfile), $pathfile . $new_name ) === true) {
            return $new_name;
        }

        return false;

    }

    public static function validate_extension($extension_image, $extensions = array()){

        if(! is_array($extensions) ){
            return false;
        }

        return in_array($extension_image, $extensions);
    }

}