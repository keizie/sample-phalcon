<?php
declare(strict_types=1);

namespace Sample\Controller\Api;

use User;

class AuthController extends \Phalcon\Mvc\Controller
{
    /**
     * 회원 로그인(인증)
     */
    public function loginAction()
    {
        return $this->response->setJsonContent(['status' => 'ok']);
    }

    /**
     * 회원 로그아웃
     */
    public function logoutAction()
    {
        $this->session->remove("user_id");
        $this->session->destroy();

        return $this->response->setJsonContent(['status' => 'ok']);
    }
}