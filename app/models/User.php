<?php

use Phalcon\Validation;
use Phalcon\Validation\Validator\Email as EmailValidator;
use Phalcon\Validation\Validator\Regex as RegexValidator;

class User extends \Phalcon\Mvc\Model
{

    /**
     * 회원 고유번호 (auto_increment)
     * @var integer
     */
    public $user_id;

    /**
     * 이름 - 한글, 영문 대소문자만 허용
     * @var string
     */
    public $name;

    /**
     * 별명 - 영문 소문자만 허용
     * @var string
     */
    public $nickname;

    /**
     * 비밀번호 - 영문 대문자, 영문 소문자, 특수 문자, 숫자 각 1개 이상씩 포함
     * @var string
     */
    public $password;

    /**
     * 전화번호 - 숫자
     * @var string
     */
    public $cellphone;

    /**
     * 이메일
     * @var string
     */
    public $email;

    /**
     * 성별
     * @var string
     */
    public $gender;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            'name',
            new RegexValidator(
                [
                    'pattern' => '/^[a-zA-z\p{Hangul}]+$/',
                    'message' => '한글, 영문 대소문자만 허용',
                ]
            )
        );

        $validator->add(
            'nickname',
            new RegexValidator(
                [
                    'pattern' => '/^[a-z]+$/',
                    'message' => '영문 소문자만 허용',
                ]
            )
        );

        $validator->add(
            'cellphone',
            new RegexValidator(
                [
                    'pattern' => '/^[0-9]+$/',
                    'message' => '숫자만 허용',
                ]
            )
        );

        $validator->add(
            'email',
            new EmailValidator(
                [
                    'model'   => $this,
                    'message' => '이메일 형식만 허용',
                ]
            )
        );

        return $this->validate($validator);
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("sample");
        $this->setSource("user");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return User[]|User|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return User|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function beforeSave()
    {
        // 숫자만 남김
        $this->cellphone = preg_replace('/[^0-9]/', '', $this->cellphone);
    }
    
    public static function passwordSanity(string $pw): bool
    {
        // 영문 대문자, 영문 소문자, 특수 문자, 숫자 각 1개 이상씩 포함
        if (preg_match('/[A-Z]/', $pw) === false
            || preg_match('/[a-z]/', $pw) === false
            || preg_match('/[A-Z]/', $pw) === false
            || preg_match('/[0-9]/', $pw) === false
        ) {
            return false;
        }
        // 최소 10자 이상
        if (strlen($pw) < 10) {
            return false;
        }
        return true;
    }
}
