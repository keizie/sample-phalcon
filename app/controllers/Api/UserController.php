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
        $gender = $this->request->getPost('gender', 'string');

        $password = trim($password);
        $password_again = trim($password_again);
        if (!User::passwordSanity($password)
            || strcmp($password, $password_again) !== 0
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
        $user->gender = $gender;

        if (!$user->save()) {
            $messages = $user->getMessages();
            $message = array_shift($messages);
            if ($message) {
                $message = $message->getMessage();
            }
            $this->response->setStatusCode(400, 'Bad Request');
            return $this->response->setJsonContent(['status' => 'fail', 'message' => $message ?: 'unknown error']);
        }

        return $this->response->setJsonContent(['status' => 'ok', 'user' => $user]);
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
        $limit = $this->request->get('limit', 'int', 10, true);
        if ($page < 1) {
            $page = 1;
        }
        if ($limit < 1 || $limit > 100) {
            $limit = 10;
        }
        $offset = ($page - 1) * $limit;

        // 이름, 이메일 검색
        $name = $this->request->get('name', 'string');
        $email = $this->request->get('email', 'email');

        if ($name) {
            $conditions[] = "name like :name_like:";
            $binds['name_like'] = '%'.$name.'%';
            $bind_types['name_like'] = \Phalcon\Db\Column::BIND_PARAM_STR;
        }
        if ($email) {
            $conditions[] = "email like :email_like:";
            $binds['email_like'] = '%'.$email.'%';
            $bind_types['email_like'] = \Phalcon\Db\Column::BIND_PARAM_STR;
        }
        if (isset($conditions, $binds, $bind_types)) {
            $params['conditions'] = implode(' and ', $conditions); // passing array trigger "SQLSTATE[HY093]: Invalid parameter number: number of bound variables does not match number of tokens"
            $params['bind'] = $binds;
            $params['bindTypes'] = $bind_types;
        }

        $params['order'] = 'user_id desc';
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $users = User::find($params);
        $userlist = [];
        foreach ($users as $user) {
            // FIXME: 개인정보 공개 범위 조정 필요, 혹은 상위권한 별도 검사
            $arr = [];
            $arr['user_id'] = $user->user_id;
            $arr['email'] = $user->email;
            $arr['name'] = $user->name;
            $arr['nickname'] = $user->nickname;
            $arr['last_order'] = Order::getLastOrder($user);
            $userlist[] = $arr;
        }

        // TODO: 전후 페이지 존재 여부 추가? prev-next url, current page
        return $this->response->setJsonContent(['status' => 'ok', 'users' => $userlist]);
    }

}