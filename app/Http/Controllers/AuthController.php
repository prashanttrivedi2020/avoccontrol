<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLanding()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('welcome');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users', 'regex:/^[a-zA-Z0-9_]+$/'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'username.required'  => __('Username is required.'),
            'username.unique'    => __('This username is already taken.'),
            'username.regex'     => __('Only letters, numbers and underscores allowed.'),
            'password.required'  => __('Password is required.'),
            'password.confirmed' => __('The passwords do not match.'),
            'password.min'       => __('Password must be at least 6 characters.'),
        ]);

        $user = User::create([
            'name'     => $request->username,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', __('Account successfully created!'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => __('Username is required.'),
            'password.required' => __('Password is required.'),
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => __('Username or password incorrect.'),
        ])->onlyInput('username');
    }

    public function demoLogin(Request $request)
    {
        $demo = User::firstOrCreate(
            ['username' => 'demo'],
            [
                'name'       => 'Demo-Benutzer',
                'email'      => 'demo@firekontrol365.de',
                'password'   => Hash::make('demo1234'),
                'role'       => 'demo',
                'store_name' => 'Demo-Supermarkt',
            ]
        );

        // Seed demo data if user is freshly created
        if ($demo->wasRecentlyCreated) {
            $this->seedDemoData($demo);
        }

        Auth::login($demo);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('info', __('You are logged in as demo user.'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome');
    }

    private function seedDemoData(User $user): void
    {
        $products = [
            ['name' => 'Vollmilch 3,5%',        'barcode' => '4000417025005', 'category' => 'Milchprodukte',  'supplier' => 'Molkerei Nord',   'purchase_price' => 0.89, 'unit' => 'L'],
            ['name' => 'Weißbrot 500g',          'barcode' => '4000417025006', 'category' => 'Backwaren',      'supplier' => 'Bäcker GmbH',     'purchase_price' => 1.29, 'unit' => 'Stk'],
            ['name' => 'Tomaten 1kg',            'barcode' => '4000417025007', 'category' => 'Gemüse',         'supplier' => 'Frische AG',      'purchase_price' => 1.99, 'unit' => 'kg'],
            ['name' => 'Schweinehack 500g',      'barcode' => '4000417025008', 'category' => 'Fleisch',        'supplier' => 'Fleischerei K.',  'purchase_price' => 2.49, 'unit' => 'Stk'],
            ['name' => 'Joghurt 500g',           'barcode' => '4000417025009', 'category' => 'Milchprodukte',  'supplier' => 'Molkerei Nord',   'purchase_price' => 0.69, 'unit' => 'Stk'],
            ['name' => 'Erdbeeren 250g',         'barcode' => '4000417025010', 'category' => 'Obst',           'supplier' => 'Frische AG',      'purchase_price' => 2.99, 'unit' => 'Stk'],
            ['name' => 'Gurke',                  'barcode' => '4000417025011', 'category' => 'Gemüse',         'supplier' => 'Frische AG',      'purchase_price' => 0.59, 'unit' => 'Stk'],
            ['name' => 'Salami 200g',            'barcode' => '4000417025012', 'category' => 'Wurst & Aufschnitt', 'supplier' => 'Fleischerei K.', 'purchase_price' => 1.89, 'unit' => 'Stk'],
        ];

        foreach ($products as $p) {
            $user->products()->create($p);
        }

        $productIds = $user->products()->pluck('id')->toArray();
        $reasons = ['verderb', 'ablauf', 'diebstahl', 'beschaedigung', 'verderb', 'ablauf', 'verderb'];

        for ($i = 0; $i < 20; $i++) {
            $pid = $productIds[array_rand($productIds)];
            $product = \App\Models\Product::find($pid);
            $user->losses()->create([
                'product_id'    => $pid,
                'loss_date'     => now()->subDays(rand(1, 90))->format('Y-m-d'),
                'quantity'      => rand(1, 10) + (rand(0, 9) / 10),
                'unit'          => $product->unit,
                'reason'        => $reasons[array_rand($reasons)],
                'supplier'      => $product->supplier,
                'purchase_price'=> $product->purchase_price,
                'notes'         => null,
            ]);
        }
    }
}
