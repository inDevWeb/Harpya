<?php
/**
 * Created by PhpStorm.
 * User: Indev
 * Date: 13/11/18
 * Time: 17:52
 */

class HRecordService extends AdiantiRecordService
{

    public function __call( $nomeDoMetodo, $argumentos )
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $reflection = new ReflectionAnnotatedClass($this);
        $requests = $reflection->getAnnotation('allowed')->value;
        $metodoExecutado = $reflection->getAnnotation('method')->value;

        if(!in_array($requests,$method)){
            throw new Exception("Metodo nao permitido para requisição $method",6001);
            exit();
        }
    }



}