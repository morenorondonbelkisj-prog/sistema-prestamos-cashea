<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User; // Usamos el modelo User que interactúa con tu tabla de PostgreSQL
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    /**
     * EVIDENCIA: API REST - Leer todos los clientes (READ ALL)
     */
    public function index()
    {
        // Relaciones ORM: Obtenemos todos los registros usando Eloquent
        $clientes = User::all();

        return response()->json([
            'success' => true,
            'mensaje' => 'Lista de clientes recuperada con éxito',
            'data'    => $clientes
        ], 200);
    }

    /**
     * EVIDENCIA: API REST - Crear un nuevo cliente (CREATE)
     */
    public function store(Request $request)
    {
        // Validamos los datos de entrada requeridos por la API
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // ORM Eloquent: Insertamos el registro de forma limpia en PostgreSQL
        $cliente = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Encriptamos la contraseña por seguridad
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Cliente creado con éxito',
            'data'    => $cliente
        ], 201);
    }

    /**
     * EVIDENCIA: API REST - Consultar un cliente específico (READ ONE)
     */
    public function show(string $id)
    {
        // ORM Eloquent: Buscamos el registro por su ID primario
        $cliente = User::find($id);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Cliente no encontrado en el sistema'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $cliente
        ], 200);
    }

    /**
     * EVIDENCIA: API REST - Actualizar datos de un cliente (UPDATE)
     */
    public function update(Request $request, string $id)
    {
        // Buscamos si el cliente existe en PostgreSQL
        $cliente = User::find($id);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Cliente no encontrado'
            ], 404);
        }

        // Validamos asegurando que si cambia el email, no choque con el de otro usuario
        $validator = Validator::make($request->all(), [
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // ORM Eloquent: Actualizamos los campos recibidos en el Request
        $cliente->update($request->all());

        return response()->json([
            'success' => true,
            'mensaje' => 'Datos del cliente actualizados con éxito',
            'data'    => $cliente
        ], 200);
    }

    /**
     * EVIDENCIA: API REST - Eliminar un cliente (DELETE)
     */
    public function destroy(string $id)
    {
        $cliente = User::find($id);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Cliente no encontrado'
            ], 404);
        }

        // ORM Eloquent: Eliminación física en la base de datos
        $cliente->delete();

        return response()->json([
            'success' => true,
            'mensaje' => 'Cliente eliminado correctamente del sistema'
        ], 200);
    }
}