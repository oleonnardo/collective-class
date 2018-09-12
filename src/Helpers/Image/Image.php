<?php

namespace Helpers\Image;

class Image {

    public static function upload($file, $path, $extensions = array(), $new_name = null, $defaultName = 'default'){

        if( empty($_FILES[$file]['name']) ){
            return $defaultName;

        } else {
            $new_name = (empty($new_name)) ? file_random_name() : $new_name;
            $new_name = $new_name . '.' . file_extension($file);
            $request->file($campo)->move($caminho, $fotoNova);
            return $new_name;

        }

    }

    public function modifica($campo, $imgAtual, $caminho){
        $request = request();

        if(Input::file($campo)){
            if( $imgAtual != 'padrao.png' ) $this->deleta($caminho . $imgAtual);

            $extensao = $this->extensao( $campo );
            $fotoNova = uniqid() . '.' . $extensao;
            $request->file($campo)->move($caminho, $fotoNova);
            return $fotoNova;

        }else{
            return $imgAtual;
        }
    }

    public function deleta( $caminho ){

        if( file_exists( $caminho ) ) File::delete( $caminho );

    }



}