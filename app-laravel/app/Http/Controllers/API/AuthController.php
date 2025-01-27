namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Walidacja danych wejściowych
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Tworzenie nowego użytkownika
        $user = User::create([
            'login' => $request->login,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generowanie tokenu
        $token = $user->createToken('auth_token')->plainTextToken;

        // Zwracanie odpowiedzi
        return response()->json([
            'message' => 'Rejestracja zakończona sukcesem',
            'user' => $user,
            'token' => $token,
        ], 201);
    }
}
