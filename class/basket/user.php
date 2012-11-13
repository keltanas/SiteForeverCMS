<?php
/**
 * Класс корзиные пользователя
 * Сохраняет данные в профиль
 * @author Nikolay Ermin
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
class Basket_User extends Basket
{
    /**
     *
     */
    public function load()
    {
        if ( $this->user->basket )
        {
            if ( ! $this->data = @unserialize( $this->user->basket ) ) {
                $this->data = array();
            }
        } else {
            $this->data = array();
        }

        // Если были данные в сессии, то сохранить их пользователю
        $basket = App::getInstance()->getSession()->get('basket');
        if ( $basket && is_array($basket) )
        {
            array_walk($basket, function($b,$i,$self) {
                $self->add($b['id'], $b['name'], $b['count'], $b['price'], $b['details']  );
            }, $this);
            App::getInstance()->getSession()->set('basket',null);
        }
        $this->save();
    }


    /**
     * Сохраняем, если добавлено в корзину
     */
    public function save()
    {
        $basket = serialize( $this->data );
        if ( $basket !== $this->user->basket ) {
            $this->user->basket   = $basket;
        }
    }
}