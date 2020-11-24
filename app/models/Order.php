<?php

class Order extends \Phalcon\Mvc\Model
{

    /**
     * 주문 고유번호 (auto_increment)
     * @var integer
     */
    public $order_id;

    /**
     * 주문한 회원 고유번호
     * @var integer
     */
    public $user_id;

    /**
     * 주문번호 - 중복이 불가능한 임의의 영문 대문자, 숫자 조합
     * @var string
     */
    public $oid;

    /**
     * 제품명 - emoji를 포함한 모든 문자
     * @var string
     */
    public $product;

    /**
     * 결제일시 - Timezone을 고려한 시간 정보
     * @var string
     */
    public $datetime;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("sample");
        $this->setSource("order");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Order[]|Order|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null): \Phalcon\Mvc\Model\ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Order|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function beforeSave()
    {
        if (!$this->oid) {
            // 12글자 중복이 불가능한 임의의 영문 대문자, 숫자 조합
            $uid = uniqid('', true); // eg. string(23) "5fbcce47e6b078.00889112"
            $body = substr($uid, -13, 4);
            $tail = substr($uid, -8);
            $this->oid = strtoupper($body.$tail);
        }
    }
}
