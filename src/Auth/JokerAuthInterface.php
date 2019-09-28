<?php


namespace OuZhou\LaravelToolGenerator\Auth;


interface JokerAuthInterface
{
    /**
     * Function: login
     * Notes: 登录
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-28  16:42
     * @param string $userId  登录者编号-主键
     * @param string $identity 身份名称
     * @param int $identityNum 身份代号
     * @param array $data 登录者详细信息
     * @return mixed
     */
    public function login(string $userId, string $identity = '', int $identityNum = 0, $data = []);

    /**
     * Function: logout
     * Notes: 退出登录
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-28  16:43
     * @return mixed
     */
    public function logout();

    /**
     * Function: checkLog
     * Notes: 检查是否登录
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-28  16:44
     * @return mixed
     */
    public function checkLogin();

    /**
     * Function: getUserId
     * Notes: 获取用户编号
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-28  16:44
     * @return mixed
     */
    public function getUserId();

    /**
     * Function: getUser
     * Notes: 获取登录者数据--不建议使用--数据不会及时刷新
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-28  16:45
     * @return mixed
     */
    public function getUser();

    /**
     * Function: getIdentity
     * Notes: 获取登录者身份
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-28  16:47
     * @return mixed
     */
    public function getIdentity();

    /**
     * Function: getIdentity
     * Notes: 获取登录者代号
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2019-09-28  16:47
     * @return mixed
     */
    public function getIdentityNum();
}