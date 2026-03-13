<?php

namespace App\Http\Controllers\Vendedor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendedorPago;
use App\Http\Controllers\Controller;

class VendedorPagoController extends Controller
{


    public function index()
    {
        $pago = VendedorPago::where('user_id', Auth::id())->first();

        return view('dashboard.vendedor.pagos', compact('pago'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'paypal_email' => 'required|email',
            'paypal_client_id' => 'nullable|string',
            'paypal_secret' => 'nullable|string'
        ]);

        VendedorPago::updateOrCreate(

            ['user_id' => Auth::id()],

            [
                'paypal_email' => $request->paypal_email,
                'paypal_client_id' => $request->paypal_client_id,
                'paypal_secret' => $request->paypal_secret
            ]

        );

        return back()->with('success', 'Cuenta PayPal guardada correctamente');
    }

}