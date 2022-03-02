<?php



namespace App\Http\Controllers\Auth;

//headers necesarios para evitar error en el cors con react.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    private $status_code    =    200;
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    //Redireccionar apí google
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    //Iniciar la sesion con google
    public function handleProviderCallback(Request $request)
    {
        //consultar si ya exite un usuario registrado en la base de datos
        $user_exits = User::where('googleId', $request->googleId)->where('external_auth', 'google')->first();
        //Condicion para solo dejar logear los que son cuentas cooporativas
        if (stristr($request->email, "@vansa.co") === false) {
            return response()->json(["status" => "failed", "success" => false, "message" => "Solo usuarios @vansa.co"]);
        } else {
            if ($user_exits) {
                //Iniciar sesion si ya existe usuario registrado
                //consulta para traer el usuario
                $user  =  User::where("email", $request->email)->first();
                //creamos el token unico
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;
                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();
                return response()->json(["status" => $this->status_code, 
                "success" => true, 
                "message" => "Inicio de sesion correcto!",
                "data" => $user,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'mytoken',
                'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()]);
            } else {
                //Si no esta registrado creamos un usuario nuevo y nos logeamos con el
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'imageUrl' => $request->imageUrl,
                    'googleId' => $request->googleId,
                    'external_auth' => 'google',
                ]);
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->token;
                if ($request->remember_me)
                    $token->expires_at = Carbon::now()->addWeeks(1);
                $token->save();
                return response()->json(["status" => $this->status_code,
                 "success" => true, 
                 "message" => "Inicio de sesion correcto!",
                 "data" => $user,
                 'access_token' => $tokenResult->accessToken,
                 'token_type' => 'mytoken',
                 'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()]);
            }
        }
    }

    //login con google laravel con blade
    // $usuario = Socialite::driver('google')->user();

    // //consultar si ya exite un usuario registrado en la base de datos
    // $user_exits = User::where('external_id', $usuario->id)->where('external_auth', 'google')->first();
    // //Condicion para solo dejar logear los que son cuentas cooporativas
    // if (stristr($usuario->email, "@vansa.co") === false) {
    //     return redirect('/login')->with('error', 'Solo se permiten usuarios @vansa.co');
    // } else {
    //     if ($user_exits) {
    //         //Iniciar sesion si ya existe usuario registrado
    //         Auth::login($user_exits);
    //     } else {
    //         //Si no esta registrado creamos un usuario nuevo y nos logeamos con el

    //         $user_new = User::create([
    //             'name' => $usuario->name,
    //             'email' => $usuario->email,
    //             'avatar' => $usuario->avatar,
    //             'external_id' => $usuario->id,
    //             'external_auth' => 'google',
    //         ]);
    //         Auth::login($user_new);
    //     }

    //     return redirect('/home');
    // }

}
