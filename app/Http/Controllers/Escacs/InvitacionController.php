<?php

namespace App\Http\Controllers\Escacs;

use Illuminate\Http\Request;
use App\Http\Controllers\Escacs\Master;
use App\Http\Controllers\Escacs\Fichas;
use App\Invitaciones;
use App\Partida;
use App\Ficha;

class InvitacionController extends Master
{
    function invitar(Request $request){
    	$userID1 = $this->getIdUserFromToken($request->input('token'));
    	$userID2 = $this->getIdUserFromName($request->input('name'));

        header("Access-Control-Allow-Origin: *");

    	if($userID1 != false && $userID2 != false){
    		$solicitudesPartida = new Invitaciones;
		    $solicitudesPartida->id_usuario1 = $userID1;
		    $solicitudesPartida->id_usuario2 = $userID2;
		    $solicitudesPartida->save();

		    $mensaje="Invitacion Enviada";
    	}else 
            $mensaje="El usuario no existe";
    	   return response(json_encode(["mensaje" => $mensaje]), 200)->header('Content-Type', 'application/json');
    }

    function ver(Request $request){
    	$id_usuario = $this->getIdUserFromToken($request->input('token'));

        header("Access-Control-Allow-Origin: *");
        
    	if($id_usuario != false){
    		$mensaje = Invitaciones::from('users as u1')
    			->join('Invitaciones as sp', function($join){
                    $join->on('u1.id', '=', 'sp.id_usuario2');
                })->join('users as u2', function($join){
                    $join->on('u2.id', '=', 'sp.id_usuario1');
                })->where('u1.id', $id_usuario)
    			->select("u2.name")
    			->get()
    			->toArray();

    	}else $mensaje="No se ha podido obtener el usuario";

    	return response(json_encode(["mensaje" => $mensaje]), 200)->header('Content-Type', 'application/json');
    }

    function responder(Request $request){
        $userID1 = $this->getIdUserFromToken($request->input('token'));
        $userID2 = $this->getIdUserFromName($request->input('name'));
        $respuesta = $request->input('respuesta');

        header("Access-Control-Allow-Origin: *");
 
        if($userID1 != false && $userID2 != false){
            if($respuesta == 1){
                Invitaciones::where([["id_usuario1", $userID2],["id_usuario2", $userID1]])->delete();

                $partida = new Partida();
                $partida->id_jugador_negro=$userID2;
                $partida->id_jugador_blanco=$userID1;
                $partida->save();

                $this->generarTablero($partida->id);

                $mensaje = "Aceptada";

            }else if($respuesta == 0){

                Invitaciones::where([["id_usuario1", $userID2],["id_usuario2", $userID1]])->delete();
                $mensaje = "Rechazada";
            }else $mensaje = "Respuesta no valida.";
        }else $mensaje="No se ha podido obtener el usuario";
        
        return response(json_encode(["mensaje" => $mensaje]), 200)->header('Content-Type', 'application/json');
    }

    private function generarTablero($idPartida){
        foreach (Fichas::getFichas() as $ficha) {
            $this->insertarFicha($idPartida, $ficha['color'], $ficha['ficha'], $ficha['fila'], $ficha['columna']);
        }
    }

    private function insertarFicha($idPartida, $color, $tipoFicha, $fila, $columna){
        $ficha = new Ficha;
        $ficha->id_partida = $idPartida;
        $ficha->color = $color;
        $ficha->tipo = $tipoFicha;
        $ficha->fila = $fila;
        $ficha->columna = $columna;
        return $ficha->save();
    }
}
