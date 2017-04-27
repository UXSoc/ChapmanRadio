<?php namespace ChapmanRadio;

use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;


class Session
{

    public static function GetCurrentUser()
    {
        return UserModel::FromId(Session::GetCurrentUserId());
    }


    public static function SetCurrentUserId($userid)
    {
        $session = new SymfonySession();

        return $session->get("userid");
    }

    public static function GetCurrentUserId()
    {
        $session = new SymfonySession();
        if($session->has("userid") == false)
            return 0;
        return $session->get("userid");
    }

    public static function IsStaff()
    {
        if (!self::HasUser()) return false;
        $user = self::GetCurrentUser();
        return $user->IsStaff();
    }

    public static function HasUser()
    {
        return Session::GetCurrentUserId() != 0;
    }

    public static function Login($userid, $redirect = '/dj')
    {
        $usermodel = UserModel::FromId($userid);
        if (!$usermodel) {
            Log::Error('PHP Logic', 'Session::Login was requested for a non existent user #' . $userid);
            return FALSE;
        }
        return Session::LoginModel($usermodel, $redirect);
    }

    public static function LoginFailed($userid, $reason)
    {
        $now = time();
        $season = Season::current();
        DB::Query("INSERT INTO suspendedloginattempts (userid,`timestamp`,type,season) VALUES ('$userid','$now','$reason','$season')");
    }

    public static function LoginModel($user, $redirect = '/dj')
    {
        $session = new SymfonySession();
        $session->set("userid",$user->id);

        $user->Update('lastlogin', date('Y-m-d H:i:s'));
        $user->Update('lastip', Request::ClientAddress());

        // If this dj is currently broadcasting, redirect
        Schedule::HandleUserLogin($user);

        // If there is a redirect in the url
        if (isset($_REQUEST['redirect'])) {
            $redirect = $_REQUEST['redirect'];
            header("Location: $redirect");
            exit;
        }

        // Otherwise, redirect to dj page
        if (isset($_SESSION['redirect'])) {
            $redirect = $_SESSION['redirect'];
            unset($_SESSION['redirect']);
            if (isset($_SESSION['redirectPageName'])) unset($_SESSION['redirectPageName']);
        }
        header("Location: $redirect");
        exit;

        return TRUE;
    }

    public static function Logout()
    {
        $session = new SymfonySession();
        $session->invalidate();
//        unset($_SESSION['userid']);
//        session_destroy();
        return TRUE;
    }

}