<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    public static function regenerateRememberToken($user)
    {
        $user->setRememberToken(bin2hex(random_bytes(32)));
        $user->save();
    }

    public static function generateTwoFactorSecret($user)
    {
        $two_factor = new Google2FA();
        $user->two_factor_secret = encrypt($two_factor->generateSecretKey());
        $user->save();
    }

    public static function generateRecoveryKeys($user)
    {

        $recovery_codes = [];
        for ($i = 0; $i < 8; $i++) {
            $recovery_codes[] = bin2hex(random_bytes(5));
        }
        $user->two_factor_recovery_codes = encrypt(json_encode($recovery_codes));
        $user->save();
    }

    public static function removeRecoveryKey($user, $key)
    {
        $recovery_codes_array = decrypt($user->two_factor_recovery_codes);
        $recovery_codes = json_decode($recovery_codes_array, true);
        $recovery_codes = array_diff($recovery_codes, [$key]);
        $user->two_factor_recovery_codes = encrypt(json_encode($recovery_codes));
        $user->save();
    }

    public static function checkTwoFactorKey($user, $key, $checkRecovery = true)
    {
        if ($key == null || $key == '') {
            return false;
        }

        if ($user->two_factor_recovery_codes == null || $user->two_factor_recovery_codes == '') {
            self::generateRecoveryKeys($user);
        }

        if ($user->two_factor_secret == null || $user->two_factor_secret == '') {
            self::generateTwoFactorSecret($user);
        }

        if ($checkRecovery) {
            $recovery_codes_array = decrypt($user->two_factor_recovery_codes);
            $recovery_codes = json_decode($recovery_codes_array, true);

            if (in_array($key, $recovery_codes)) {
                self::removeRecoveryKey($user, $key);
                return true;
            }
        }

        $two_factor = new Google2FA();
        $two_factor_secret = decrypt($user->two_factor_secret);
        $valid = $two_factor->verifyKey($two_factor_secret, $key);
        if (!$valid) {
            return false;
        }

        return true;
    }
}