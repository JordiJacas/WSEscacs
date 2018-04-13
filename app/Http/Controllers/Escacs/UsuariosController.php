<?php

namespace App\Http\Controllers\Escacs;

use Illuminate\Http\Request;
use App\Http\Controllers\Escacs\Master;
use Auth;
use App\Ficha;
use App\User;

class UsuariosController extends Master
{
    function login(Request $request){
    	$password = $request->input('password');
    	$email = $request->input('email');

        // Activem CORS
        header("Access-Control-Allow-Origin: *");

    	if (Auth::attempt(['email' => $email, 'password' => $password])){
	    	$token = $this->generateToken();
            User::where([['id', Auth::id()], ['token', null]])->update(array('token' => $token));
            $mensaje = "Session Iniciada";
        }else{
	    	$mensaje = "Email o contraseña incorrecta";
        }
        return response(json_encode(["mensaje" => $mensaje]), 200)->header('Content-Type', 'application/json');
    		
    }

    function logout(Request $request){
    	$token = $request->input('token');
    	$mensaje = "Session Cerradas";

        // Activem CORS
        header("Access-Control-Allow-Origin: *");
        
    	return response(json_encode(["mensaje" => $mensaje]), 200)->header('Content-Type', 'application/json');
    }


    function conectados(Request $request){
        $id_usuario = $this->getIdUserFromToken($request->input('token'));
        if($id_usuario != false){
            $consulta = User::select("name")
                  ->where([["token", "<>", "null"],["id", "<>", $id_usuario]])
                  ->get();

            $usernames = [];
            foreach ($consulta as $value) {
                $usernames[] = $value["name"];
            }
            return response(json_encode(["usernames" => $usernames]), 200)->header('Content-Type', 'application/json');
        }else 

            $mensaje="No se ha podido obtener el usuario";
            return response(json_encode(["mensaje" => $mensaje]), 200)->header('Content-Type', 'application/json');            
    }

    private function generateToken(){
    	do{
    		$token = md5(uniqid(rand(), true));
    	}while(User::where("token", $token)->count() >= 1);

    	return $token;
    }
}
