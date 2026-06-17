<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\JournalAudit;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function show()
    {
        if (!session('2fa_required')) {
            return redirect()->route('dashboard');
        }
        return view('auth.2fa-verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $user = auth()->user();
        $valid = $this->google2fa->verifyKey($user->google2fa_secret, $request->otp);

        if (!$valid) {
            return back()->withErrors(['otp' => 'Code invalide ou expiré. Veuillez réessayer.']);
        }

        session()->forget('2fa_required');
        session(['2fa_verified' => true]);
        JournalAudit::enregistrer('2FA_VERIFICATION', '2FA vérifié avec succès');

        return redirect()->intended(route('dashboard'));
    }

    public function setup()
    {
        $user   = auth()->user();
        $secret = $this->google2fa->generateSecretKey();
        session(['2fa_secret_temp' => $secret]);

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer   = new Writer($renderer);
        $qrSvg    = $writer->writeString($qrCodeUrl);

        return view('auth.2fa-setup', compact('secret', 'qrSvg'));
    }

    public function enable(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $secret = session('2fa_secret_temp');
        $valid  = $this->google2fa->verifyKey($secret, $request->otp);

        if (!$valid) {
            return back()->withErrors(['otp' => 'Code invalide. Veuillez scanner à nouveau le QR code.']);
        }

        $user = auth()->user();
        $user->update(['google2fa_secret' => $secret, 'google2fa_enabled' => true]);
        session()->forget('2fa_secret_temp');
        session(['2fa_verified' => true]);
        JournalAudit::enregistrer('2FA_ACTIVATION', '2FA activé sur le compte');

        return redirect()->route('dashboard')->with('success', 'Authentification à deux facteurs activée avec succès.');
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (!\Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }

        auth()->user()->update(['google2fa_secret' => null, 'google2fa_enabled' => false]);
        session()->forget('2fa_verified');
        JournalAudit::enregistrer('2FA_DESACTIVATION', '2FA désactivé sur le compte');

        return back()->with('success', '2FA désactivé.');
    }
}
