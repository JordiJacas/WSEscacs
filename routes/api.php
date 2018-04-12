<?php

use Illuminate\Http\Request;
use App\User;
/*
|--------------------------------------------------------------------------
| API Routes
|---------------------------5-----------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

$path = "Escacs\\";

/*
|------------------------------------------------
| Parametros para UsiariosController:
|------------------------------------------------
|	Login -> email, password
|	logout -> token
|	Conectados -> token
|------------------------------------------------
*/

Route::get('/usuarios/login', $path.'UsuariosController@login');
Route::get('/usuarios/logout', $path.'UsuariosController@logout');
Route::get('/usuarios/conectados', $path.'UsuariosController@conectados');


/*
|------------------------------------------------
| Parametros para InvitacionController:
|------------------------------------------------
|	invitar -> token, name
|	ver -> token
|	responder -> token, name, respuesta (0,1)
|------------------------------------------------
*/
Route::get('/invitacion/invitar', $path.'InvitacionController@invitar');
Route::get('/invitacion/ver', $path.'InvitacionController@ver');
Route::get('/invitacion/responder', $path.'InvitacionController@responder');


/*
|------------------------------------------------
| Parametros para TableroController:
|------------------------------------------------
|	ver -> token, name
|	mover -> token, name, toFila, toColumna, fromFila, fromColumna
|------------------------------------------------
*/

Route::get('/tablero/ver', $path.'TableroController@ver');
Route::get('/tablero/mover', $path.'TableroController@moverFicha');