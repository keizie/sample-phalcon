<?php

use Phalcon\Mvc\ModelInterface;

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
     * 결제일시 - 현지 시간 정보
     * @var string
     */
    public $datetime_local;

    /**
     * 결제일시 - UTC 시간 정보
     * @var string
     */
    public $datetime_utc;

    /**
     * 결제일시 - 현지 타임존 정보
     * @var string
     */
    public $datetime_timezone;

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
    public static function findFirst($parameters = null): ?ModelInterface
    {
        return parent::findFirst($parameters);
    }

    public function beforeValidation()
    {
        if (!$this->oid) {
            // 12글자 중복이 불가능한 임의의 영문 대문자, 숫자 조합
            $uid = uniqid('', true); // eg. string(23) "5fbcce47e6b078.00889112"
            $body = substr($uid, -13, 4);
            $tail = substr($uid, -8);
            $this->oid = strtoupper($body.$tail);
        }
        if (!$this->datetime_local
            && !$this->datetime_utc
            && !$this->datetime_timezone
        ) {
            /* @see https://stackoverflow.com/a/59004019/6629746 */
            // 사용자에게 출력할 때는 현지 시각을 빈번하게 사용해야 하므로 DST 변동 등의 영향을 최소화하기 위해 현지 시각도 유지
            $this->datetime_local = date('Y-m-d H:i:s');
            $this->datetime_utc = gmdate('Y-m-d H:i:s');
            $this->datetime_timezone = date_default_timezone_get();
        }
    }

    public static function getLastOrder(User $user): ?self
    {
        $order = self::findFirst([
            'conditions' => "user_id = {$user->user_id}",
            'order' => "order_id desc",
            'limit' => 1
        ]);
        return $order ?? null;
    }
}
