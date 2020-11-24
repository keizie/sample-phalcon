<?php
declare(strict_types=1);

namespace Sample\Controller\Api;

use User;
use Order;

class UserController extends \Phalcon\Mvc\Controller
{

    /**
     * 회원 가입
     */
    public function createAction()
    {
        $name = $this->request->getPost('name', 'string');
        $nickname = $this->request->getPost('nickname', 'string');
        $password = $this->request->getPost('password', 'string', '');
        $password_again = $this->request->getPost('password2', 'string', '');
        $cellphone = $this->request->getPost('cellphone', 'string');
        $email = $this->request->getPost('email', 'email');

        $password = trim($password);
        $password_again = trim($password_again);
        if (!User::passwordSanity($password)
            || strcmp($password, $password_again) === 0
        ) {
            $this->response->setStatusCode(400, 'Bad Request');
            return $this->response->setJsonContent(['status' => 'fail', 'message' => 'wrong password']);
        }

        $user = new User();
        $user->name = $name;
        $user->nickname = $nickname;
        $user->password = $this->security->hash($password);
        $user->cellphone = $cellphone;
        $user->email = $email;

        if (!$user->save()) {
            $messages = $user->getMessages();
            $message = array_shift($messages) ?: '';
            $this->response->setStatusCode(400, 'Bad Request');
            return $this->response->setJsonContent(['status' => 'fail', 'message' => $message]);
        }

        return $this->response->setJsonContent($user);
    }

    /**
     * 단일 회원 상세 정보 조회
     */
    public function readAction($user_id = null)
    {
        if (!is_numeric($user_id)) {
            $this->response->setStatusCode(400, 'Bad Request');
            return $this->response->setJsonContent(['status' => 'fail', 'message' => 'wrong user']);
        }
        $user = User::findFirst("user_id = $user_id");
        return $this->response->setJsonContent(['status' => 'ok', 'user' => $user ?: null]);
    }

    /**
     * 단일 회원의 주문 목록 조회
     */
    public function ordersAction($user_id = null)
    {
        if (!is_numeric($user_id)) {
            $this->response->setStatusCode(400, 'Bad Request');
            return $this->response->setJsonContent(['status' => 'fail', 'message' => 'wrong user']);
        }
        $orders = Order::find("user_id = {$user_id}");
        return $this->response->setJsonContent(['status' => 'ok', 'orders' => $orders ?: []]);
    }

    /**
     * 여러 회원 목록 조회
     */
    public function indexAction()
    {
        $page = $this->request->get('page', 'int', 1, true);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // 이름, 이메일 검색
        $name = $this->request->get('name', 'string');
        $email = $this->request->get('email', 'email');

        if ($name) {
            $conditions[] = "name = :name:";
            $binds['name'] = $name;
        }
        if ($email) {
            $conditions[] = "email = :email:";
            $binds['email'] = $email;
        }
        if (isset($conditions, $binds)) {
            $users = User::find([
                'conditions' => $conditions,
                'bind' => $binds,
                'limit' => ['number' => $limit, 'offset' => $offset],
            ]);
        }

        return $this->response->setJsonContent(['status' => 'ok', 'users' => $users ?? []]);
    }

}