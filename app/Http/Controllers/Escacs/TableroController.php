<?php

namespace App\Http\Controllers\Escacs;  

use Illuminate\Http\Request;
use App\Http\Controllers\Escacs\Master;
use App\Http\Controllers\Escacs\Fichas;
use App\User;
use App\Partida;
use App\Ficha;

class TableroController extends Master
{
    function ver(Request $request){
        $userID1 = $this->getIdUserFromToken($request->input('token'));
        $userID2 = $this->getIdUserFromName($request->input('name'));

        header("Access-Control-Allow-Origin: *");

        $fichas = "";

        if($userID1 != false && $userID2 != false){
            $partida = Partida::select("id")->where([["id_jugador_negro", $userID1],["id_jugador_blanco", $userID2]])->orWhere([["id_jugador_negro", $userID2],["id_jugador_blanco", $userID1]]);
            if($partida->count() > 0){
                $idPartida = $partida->first()->toArray()["id"];
                $fichas = Ficha::select("color", "tipo", "fila", "columna")->where("id_partida", $idPartida)->get()->toArray();
                $mensaje = "Partida encontrada";
            }else{ $mensaje = "No se ha encontrado la partida.";}
        }else{$mensaje="No se ha podido obtener el usuario";}

        return response(json_encode(["mensaje"=>$mensaje, "tablero" => $fichas]), 200)->header('Content-Type', 'application/json');
    }

    function moverFicha(Request $request){
        $userID1 = $this->getIdUserFromToken($request->input('token'));
        $userID2 = $this->getIdUserFromName($request->input('name'));
        $toFila = $request->input('toFila');
        $toColumna = $request->input('toColumna');
        $fromFila = $request->input('fromFila');
        $fromColumna = $request->input('fromColumna');

        header("Access-Control-Allow-Origin: *");

        if($userID1 != false && $userID2 != false){
            $partida = Partida::select("id", "turno", "id_jugador_negro", "id_jugador_blanco")
                        ->where([["id_jugador_negro", $userID1], ["id_jugador_blanco", $userID2]])
                        ->orWhere([["id_jugador_negro", $userID2], ["id_jugador_blanco", $userID1]]);
            if($partida->count() == 1){
                $partida = $partida->first();
                
                if(($partida->turno === "n" && $partida->id_jugador_negro == $userID1) || 
                   ($partida->turno === "b" && $partida->id_jugador_blanco == $userID1)){
                    $ficha = Ficha::where([["id_partida", $partida->id], ["fila", $toFila], ["columna", $toColumna], ["color", $partida->turno]]);
                    if($ficha->count() == 1){
                        $fichaTarget = Ficha::where([["id_partida", $partida->id], ["fila", $fromFila], ["columna", $fromColumna]]);

                        $ficha = $ficha->first();
                        $ficha->columna = $fromColumna;
                        $ficha->fila = $fromFila;
                        $ficha->save();

                        $partida->turno = ($partida->turno === "n" ? "b" : "n");
                        $partida->save();

                        $mensaje="ficha movida";
                    }
                }else{$mensaje = "No es tu turno.";}
            }else{$mensaje = "No se ha encontrado la partida.";}
        }else{$mensaje="No se ha podido obtener el usuario";}
        
       return response(json_encode(["mensaje" => $mensaje]), 200)->header('Content-Type', 'application/json');
    }
}
