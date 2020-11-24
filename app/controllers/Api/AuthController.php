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
        if ($this->session->get('user_id')) {
            return $this->response->setJsonContent(['status' => 'ok', 'user_id' => $this->session->get('user_id')]);
        }

        $email = $this->request->getPost('email', ['email', 'trim']);
        $password = $this->request->getPost('password', 'trim');
        if (!$email || !$password) {
            $this->response->setStatusCode(400, 'Bad Request');
            return $this->response->setJsonContent(['status' => 'fail', 'message' => 'wrong login 1']);
        }

        $user = User::findFirst([
            'conditions' => 'email = :email:',
            'bind' => ['email' => $email],
            'bindTypes' => ['email' => \Phalcon\Db\Column::BIND_PARAM_STR]
        ]);
        if (!$user) {
            $this->response->setStatusCode(400, 'Bad Request');
            return $this->response->setJsonContent(['status' => 'fail', 'message' => 'wrong login 2']);
        }

        if (!$this->security->checkHash($password, $user->password)) {
            $this->response->setStatusCode(400, 'Bad Request');
            return $this->response->setJsonContent(['status' => 'fail', 'message' => 'wrong login 3']);
        }

        $this->session->set('user_id', $user->user_id);
        return $this->response->setJsonContent(['status' => 'ok']);
    }

    /**
     * 회원 로그아웃
     */
    public function logoutAction()
    {
        $this->session->remove('user_id');
        $this->session->destroy();

        return $this->response->setJsonContent(['status' => 'ok']);
    }
}