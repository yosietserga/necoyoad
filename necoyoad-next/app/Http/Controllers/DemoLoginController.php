<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * DemoLoginController — quick-login picker for demo accounts.
 *
 * Shows all demo accounts (admin + customer) with one-click auto-login.
 * Only available when APP_ENV=local (dev mode).
 */
class DemoLoginController extends Controller
{
    public function index()
    {
        if (config('app.env') !== 'local') {
            abort(404);
        }

        $admins = User::where('status', true)->get(['id', 'username', 'firstname', 'lastname', 'email']);
        $customers = Customer::where('status', true)->get(['id', 'firstname', 'lastname', 'email', 'store_id']);
        $stores = Store::where('status', true)->get(['id', 'name', 'folder']);

        return view('auth.demo-login', compact('admins', 'customers', 'stores'));
    }

    public function loginAdmin(Request $request, int $id)
    {
        if (config('app.env') !== 'local') {
            abort(404);
        }

        $user = User::findOrFail($id);
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        return redirect('/admin');
    }

    public function loginCustomer(Request $request, int $id)
    {
        if (config('app.env') !== 'local') {
            abort(404);
        }

        $customer = Customer::findOrFail($id);
        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('common.home');
    }
}
