<?php

namespace App\Http\Middleware;

use App\Models\LampiranFile;
use App\Models\PenerimaBanmod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }



    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'roles' => $request->user()->roles->pluck('name'),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                ] : null,
            ],
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'env' => [
                'app_url_esuket' => env('APP_URL_ESUKET'),
                'app_email_banmod' => env('APP_EMAIL_BANMOD'),
                'app_wa_banmod' => env('APP_WA_BANMOD'),
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'can' => $request->user() ? [
                'viewPemasukan' => $request->user()->can('view-pemasukan'),
                'createPemasukan' => $request->user()->can('add-pemasukan'),
                'editPemasukan' => $request->user()->can('edit-pemasukan'),
                'deletePemasukan' => $request->user()->can('delete-pemasukan'),

                'viewPengeluaran' => $request->user()->can('view-pengeluaran'),
                'createPengeluaran' => $request->user()->can('add-pengeluaran'),
                'editPengeluaran' => $request->user()->can('edit-pengeluaran'),
                'deletePengeluaran' => $request->user()->can('delete-pengeluaran'),

                'viewLaporan' => $request->user()->can('view-laporan'),
                'createLaporan' => $request->user()->can('add-laporan'),
                'editLaporan' => $request->user()->can('adit-laporan'),
                'deleteLaporan' => $request->user()->can('delete-laporan'),

                'viewQurban' => $request->user()->can('view-qurban'),
                'createQurban' => $request->user()->can('add-qurban'),
                'editQurban' => $request->user()->can('edit-qurban'),
                'deleteQurban' => $request->user()->can('delete-qurban'),

                'viewDashboard' => $request->user()->can('view-dashboard'),

                'viewMaster' => $request->user()->can('view-master'),
                'createMaster' => $request->user()->can('add-master'),
                'editMaster' => $request->user()->can('edit-master'),
                'deleteMaster' => $request->user()->can('delete-master'),

                'viewUser' => $request->user()->can('view-user'),
                'createUser' => $request->user()->can('add-user'),
                'editUser' => $request->user()->can('edit-user'),
                'deleteUser' => $request->user()->can('delete-user'),

                'viewRole' => $request->user()->can('view-role'),
                'createRole' => $request->user()->can('add-role'),
                'editRole' => $request->user()->can('edit-role'),
                'deleteRole' => $request->user()->can('delete-role'),
            ] : [],
        ];
    }
}
