<?php
/**
 * Created by PhpStorm.
 * User: royalwang
 * Date: 2018/12/6
 * Time: 17:17
 */

namespace Ecjia\System\Admins\RememberPassword;

use Ecjia\System\Admins\Users\AdminUserModel;
use Ecjia\System\Admins\Users\Password;
use ecjia;
use RC_Cookie;

class RememberPassword
{

    const COOKIE_ADMIN_USER_HASH = 'ADMIN_USER_HASH';

    protected $lifetime = 3600 * 24 * 7;

    public function __construct()
    {

    }

    /**
     * 记住密码
     */
    public function remember($user_id, $password)
    {
        $password = Password::createSaltPassword($password, ecjia::config('hash_code'));

        $json_string = json_encode([$user_id, $password]);

        RC_Cookie::set(self::COOKIE_ADMIN_USER_HASH, $json_string, array('expire' => $this->lifetime));
    }


    /**
     * 删除记住的密码
     */
    public function delete()
    {
        RC_Cookie::delete(self::COOKIE_ADMIN_USER_HASH);
    }

    /**
     * 验证记住的密码
     *
     * @return bool
     */
    public function verification($callback = null)
    {
        $json_string = RC_Cookie::get(self::COOKIE_ADMIN_USER_HASH);

        if (empty($json_string)) {
            return false;
        }

        list($user_id, $password) = json_decode($json_string, true);

        if (! empty($user_id) && ! empty($password)) {

            $model = AdminUserModel::find($user_id);
            if (!empty($model)) {
                $password_hash = Password::createSaltPassword($model->password, ecjia::config('hash_code'));

                if ($password == $password_hash) {

                    if (is_callable($callback)) {
                        $callback($model);
                    }

                    return true;
                }
                else {
                    $this->delete();
                }
            }
            else {
                $this->delete();
            }
        }
        else {
            $this->delete();
        }

        return false;
    }

}