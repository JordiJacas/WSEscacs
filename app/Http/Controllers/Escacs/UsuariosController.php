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
            
            return response(json_encode(["mensaje" => $mensaje, "token" => $token]), 200)->header('Content-Type', 'application/json');
        }else{

	    	$mensaje = "Email o contraseña incorrecta";
            return response(json_encode(["mensaje" => $mensaje]), 200)->header('Content-Type', 'application/json');
        }
    		
    }

    function logout(Request $request){
        $id_usuario = $this->getIdUserFromToken($request->input('token'));
        $token = $request->input('token');
        // Activem CORS
        header("Access-Control-Allow-Origin: *");

        User::where('token', $token)->update(array('token' => null));
        $mensaje = "Session cerrada";
        
    	return response(json_encode(["mensaje" => $mensaje]), 200)->header('Content-Type', 'application/json');
    }


    function conectados(Request $request){
        $id_usuario = $this->getIdUserFromToken($request->input('token'));

        // Activem CORS
        header("Access-Control-Allow-Origin: *");

        if($id_usuario != false){
            $consulta = User::select("name")
                  ->where([["token", "<>", "null"],["id", "<>", $id_usuario]])
                  ->get();

            $usernames = [];
            foreach ($consulta as $value) {
                $usernames[] = $value["name"];
            }
            return response(json_encode(["usernames" => $usernames]), 200)->header('Content-Type', 'application/json');
        }else{
            $mensaje="No se ha encontrado el usuario actual";
            return response(json_encode(["id_usuario" => $id_usuario, "mensaje" => $mensaje]), 200)->header('Content-Type', 'application/json');
        }            
    }

    private function generateToken(){
    	do{
    		$token = md5(uniqid(rand(), true));
    	}while(User::where("token", $token)->count() >= 1);

    	return $token;
    }
}
