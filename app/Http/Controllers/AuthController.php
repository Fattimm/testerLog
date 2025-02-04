<?php

namespace App\Http\Controllers;

use App\Traits\LogsActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller 
{
    use LogsActions;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $this->addLogDetails([
                'validation_errors' => $validator->errors()->toArray(),
                'submitted_data' => $request->except('password')
            ]);

            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if (!Auth::attempt($validated)) {
            $this->addLogDetails([
                'email' => $validated['email'],
                'error' => 'Identifiants invalides',
                'submitted_data' => ['email' => $validated['email']]
            ]);

            return response()->json([
                'message' => 'Erreur d\'authentification : Email ou mot de passe incorrect',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('Personal Access Token')->accessToken;

        $this->addLogDetails([
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            $this->addLogDetails([
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            // $request->user()->token()->revoke();
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Déconnexion réussie',
            ]);
        } catch (\Exception $e) {
            $this->addLogDetails([
                'error_message' => $e->getMessage(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'message' => 'Erreur lors de la déconnexion',
            ], 500);
        }
    }
}
