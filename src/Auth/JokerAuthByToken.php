<?php

namespace OuZhou\LaravelToolGenerator\Auth;

class JokerAuthByToken implements JokerAuthInterface
{
    protected $headerName = 'X-JOKER-TOKEN';

    /**
     * Function: getCookie
     * Notes: 返回储存着cookie的token
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-07  17:45
     * string $userId
     * string $identity 登录者身份
     * int $identityNum
     * array $data
     * @return array
     * @throws \Exception
     */
    public function login(string $userId, string $identity = '', int $identityNum = 0, $data = []): array
    {
        // 存入session
        $secretKey = bcrypt(bcrypt(time() . $userId . random_bytes(20)));
        session([
            $secretKey => [
                'id' => $userId,
                'identity' => $identity,
                'identityNum' => $identityNum,
                'data' => $data
            ]
        ]);
        // 自定义验证字段--存入请求头中
        return [$this->headerName => $secretKey];
    }

    /**
     * Function: logout
     * Notes: 退出登录
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-07  17:55
     * @return bool
     */
    public function logout()
    {
        if ($key = request()->header($this->headerName)) {
            session()->forget($key);
            return true;
        }
        return false;
    }

    /**
     * Function: checkLogin
     * Notes: 检查是否登录了或者登录失败
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-07  17:50
     * @return bool
     */
    public function checkLogin()
    {
        if ($key = request()->header($this->headerName)) {
            if (session($key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Function: getUserId
     * Notes: 返回登录者id
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-07  18:24
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed|null
     */
    public function getUserId()
    {
        if ($key = request()->header($this->headerName)) {
            if ($temp = session($key)) {
                return $temp['id'];
            }
        }
        return null;
    }

    /**
     * Function: getUser
     * Notes: 返回登录者信息
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-12  17:16
     * @return |null
     */
    public function getUser()
    {
        if ($key = request()->header($this->headerName)) {
            if ($temp = session($key)) {
                return $temp['data'];
            }
        }
        return null;
    }

    /**
     * Function: getIdentity
     * Notes: 返回登录者身份
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-08  11:14
     * @return |null
     */
    public function getIdentity()
    {
        if ($key = request()->header($this->headerName)) {
            if ($temp = session($key)) {
                return $temp['identity'];
            }
        }
        return null;
    }

    /**
     * Function: getIdentityNum
     * Notes: 返回登录者身份 number
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-08-12  12:07
     * @return int
     */
    public function getIdentityNum()
    {
        if ($key = request()->header($this->headerName)) {
            if ($temp = session($key)) {
                return $temp['identityNum'];
            }
        }
        return 0;
    }

}