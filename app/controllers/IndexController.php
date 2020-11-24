<?php
declare(strict_types=1);

class IndexController extends ControllerBase
{

    public function indexAction()
    {

    }

    public function dummyOrderAction()
    {
        $user_id_min = (int)User::minimum(['column' => 'user_id']);
        $user_id_max = (int)User::maximum(['column' => 'user_id']);
        $i = 100;
        while ($i-->0) {
            $order = new Order();
            $order->product = '🎐📚🚉🌋🐤🎫☘🔐📬🥈🐽🎁⛄📓🌡🎣🏵📅🎱🌫📺🍍🌱⛲💈🌡📕🗃🌌🥗';
            $order->user_id = rand($user_id_min, $user_id_max);
            if (!$order->save()) {
                foreach ($order->getMessages() as $message) {
                    var_dump($message->getMessage());
                }
            }
        }
    }

}

