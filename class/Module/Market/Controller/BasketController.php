<?php
/**
 * Контроллер корзины
 * @author KelTanas
 * @link http://siteforever.ru
 * @link http://ermin.ru
 */
namespace Module\Market\Controller;

use Module\Market\Model\OrderPositionModel;
use Module\Market\Object\OrderPosition;
use Sfcms;
use Sfcms\Controller;
use Sfcms\Form\Form;
use Forms_Basket_Address;
use Module\Market\Object\Delivery;
use Module\Market\Model\OrderModel;
use Module\Catalog\Model\CatalogModel;

class BasketController extends Controller
{
    public function indexAction( $address )
    {
        $form = new Forms_Basket_Address();
        $form->delivery_id = $this->request->getSession()->get('delivery');

        // Ajax validate
        if ( $this->request->isAjax() && $form->getPost() ) {
            return $this->ajaxValidate( $form );
        }

        // Fill Address from current user
        if ( $this->user->hasPermission( USER_USER ) ) {
            $form->getField('fname')->setValue( $this->user->fname );
            $form->getField('lname')->setValue( $this->user->lname );
            $form->getField('email')->setValue( $this->user->email );
            $form->getField('phone')->setValue( $this->user->phone );
            $form->getField('address')->setValue( $this->user->address );
        }

        // Fill Address from Yandex
        if ( $address ) {
            $form->setData( $this->fromYandexAddress( $address ) );
        }

//        $catalogModel    = $this->getModel('Catalog');

        $this->request->setTitle($this->t('basket','Basket'));
        $this->request->set('template', 'inner');

        $this->getTpl()->getBreadcrumbs()
            ->addPiece('index',$this->t('Home'))
            ->addPiece(null,$this->request->getTitle());

        // Заполним методы доставки
        $deliveryModel = $this->getModel('Delivery');
        $deliveries = $deliveryModel->findAll('active = ?',array(1),'pos');
        $form->getField('delivery_id')->setVariants( $deliveries->column('name') );

        $delivery = $this->app()->getDelivery($this->request);
        $form->getField('delivery_id')->setValue( $delivery->getType() );

        // Заполним методы оплаты
        $paymentModel = $this->getModel('Payment');
        $payments = $paymentModel->findAll('active = ?', array(1));
        $form->getField('payment_id')->setVariants( $payments->column('name') );
        $form->getField('payment_id')->setValue( $payments->rewind() ? $payments->rewind()->getId() : 0 );

        $metroModel = $this->getModel('Metro');
        $metro      = $metroModel->findAll('city_id = ?',array(2),'name');
        $form->getField('metro')->setVariants( $metro->column('name') );

        // Список ID продуктов
        $productIds = array_filter( array_map(function($b){
            return isset( $b['id'] ) ? $b['id'] : false;
        },$this->getBasket()->getAll()) );

        // Получаем товары из каталога
        /** @var $catalogModel CatalogModel */
        $catalogModel   = $this->getModel('Catalog');
        $products       = count($productIds)
            ? $catalogModel->findAll('id IN (?)', array($productIds))
            : $catalogModel->createCollection();

//        $this->log( $productIds, 'basket' );
//        $this->log( $this->getBasket()->getAll(), 'basket' );

        return array(
            'products'      => $products,
            'all_product'   => array_map(function( $prod ) use ($products) {
                                    return array_merge( $prod, array('obj'=>$products->getById($prod['id'])) );
                                }, $this->getBasket()->getAll()),
            'all_count'     => $this->getBasket()->getCount(),
            'all_summa'     => $this->getBasket()->getSum(),
            'delivery'      => $delivery,
            'form'          => $form,
            'host'          => urlencode($this->config->get('siteurl').$this->router->createLink('basket') ),
        );
    }


    /**
     * Добавит в корзину товар
     *
     * @param $_REQUEST['basket_prod_id']
     * @param $_REQUEST['basket_prod_name']
     * @param $_REQUEST['basket_prod_count']
     * @param $_REQUEST['basket_prod_price']
     * @param $_REQUEST['basket_prod_details']
     *
     * @return string
     */
    public function addAction()
    {
        $post = $this->request->request;

        $basket_prod_name = $post->get('basket_prod_name');
        $basket_prod_id   = $post->get('basket_prod_id');
        if ( $basket_prod_id || $basket_prod_name ) {
            $basket_prod_count      = $post->get('basket_prod_count');
            $basket_prod_price      = $post->get('basket_prod_price');
            $basket_prod_details    = $post->get('basket_prod_details');

            $this->getBasket()->add(
                $basket_prod_id,
                $basket_prod_name,
                $basket_prod_count,
                $basket_prod_price,
                $basket_prod_details
            );

            $this->getBasket()->save();
        }

        $this->getTpl()->assign(array(
            'count'     => $this->getBasket()->getCount(),
            'summa'     => $this->getBasket()->getSum(),
            'number'    => $this->getBasket()->count(),
            'path'      => $this->request->get('path'),
        ));

        return array(
            'id'     => $basket_prod_id,
            'count'  => $this->getBasket()->getCount( $basket_prod_id ),
            'widget' => $this->getTpl()->fetch('basket.widget'),
            'msg'    => $basket_prod_name . '<br>'
                      . Sfcms::html()->link('добавлен в корзину',$this->router->createServiceLink('basket','index')),
        );

    }


    /**
     * Ajax validate
     * @param Form $form
     * @return array
     */
    private function ajaxValidate( Form $form )
    {
        $result = array('error'=>0);

        if ( $this->request->get('recalculate') ) {
            // обновляем количества
            $basket_counts = $this->request->get('basket_counts');

            if ( $basket_counts && is_array( $basket_counts ) ) {
                /** @var $basket Sfcms\Basket\Base */
                array_walk($basket_counts, function($prod_count, $key, $basket){
                    $basket->setCount( $key, $prod_count > 0 ? $prod_count : 1 );
                }, $this->getBasket());
            }

            // Удалить запись
            $basket_del = $this->request->get('basket_del');
            if ( $basket_del && is_array( $basket_del ) ) {
                foreach( $basket_del as $key => $prod_del ) {
                    $this->getBasket()->del( $key );
                    $result['delete'][] = $key;
                }
            }

            $delivery = $this->app()->getDelivery($this->request);
            $result['delivery']['cost'] = number_format( $delivery->cost(), 2, ',', '' );
            $result['basket'] = $this->getBasket()->getAll();
            $result['basket']['sum'] = $this->getBasket()->getSum() + $delivery->cost();
            $result['basket']['count'] = $this->getBasket()->getCount();
            $result['basket']['delitems'] = isset($result['delete']) ? $result['delete'] : array();
            unset( $result['delete'] );
            $this->getBasket()->save();
        }

        if ( $this->request->get('do_order') ) {
            if ( $form->validate() ) {
                // Создание заказа
                if ( $this->getBasket()->getAll() ) {
                    // создать заказ

                    $delivery = $this->app()->getDelivery($this->request);
                    $this->request->getSession()->set('delivery',$delivery->getType());

                    /** @var $orderModel OrderModel */
                    $orderModel    = $this->getModel('Order');
                    $order = $orderModel->createOrder($form, $delivery);

                    if ($order) {
                        /** @var $orderPositionModel OrderPositionModel */
                        $orderPositionModel = $this->getModel('OrderPosition');
                        // Заполняем заказ товарами

                        $pos_list    = array();
                        foreach ($this->getBasket()->getAll() as $data) {
                            /** @var $position OrderPosition */
                            $position   = $orderPositionModel->createObject();
                            $position->attributes = array(
                                'ord_id'    => $order->getId(),
                                //                    'name'      => $data['name'],
                                'product_id'=> (int) $data['id'],
                                'articul'   => ! empty( $data['articul'] ) ? $data['articul'] : $data['name'],
                                'details'   => $data['details'],
                                'currency'  => isset( $data['currency'] ) ? $data['currency'] : $this->t('catalog','RUR'),
                                'item'      => isset( $data['item'] ) ? $data['item'] : $this->t('catalog', 'item'),
                                'cat_id'    => is_numeric( $data['id'] ) ? $data['id'] : '0',
                                'price'     => $data['price'],
                                'count'     => $data['count'],
                                'status'    => 1,
                            );
                            $position->save();

                            $pos_list[] = $position->attributes;
                        }

                        $this->app()->getTpl()->assign(array(
                                'order'     => $order,
                                'sitename'  => $this->config->get('sitename'),
                                'ord_link'  => $this->app()->getConfig()->get('siteurl').$order->getUrl(),
                                'user'      => $this->app()->getAuth()->currentUser()->getAttributes(),
                                'date'      => date('H:i d.m.Y'),
                                'order_n'   => $order->getId(),
                                'positions' => $pos_list,
                                'total_summa'=> $this->getBasket()->getSum() + $delivery->cost(),
                                'total_count'=> $this->getBasket()->getCount(),
                                'delivery'  => $delivery,
                                'sum'       => $this->getBasket()->getSum(),
                            ));

                        $this->sendmail(
                            $order->email,
                            $this->config->get('admin'),
                            sprintf('Новый заказ с сайта %s №%s',$this->config->get('sitename'),$order->getId()),
                            $this->app()->getTpl()->fetch('order.mail.createadmin')
                        );

                        $this->sendmail(
                            $this->config->get('admin'),
                            $order->email,
                            sprintf('Заказ №%s на сайте %s',$order->getId(),$this->config->get('sitename')),
                            $this->app()->getTpl()->fetch('order.mail.create')
                        );

                        $this->getBasket()->clear();
                        $this->getBasket()->save();

                        $this->request->getSession()->set('order_id',$order->id);

                        $paymentModel = $this->getModel('Payment');
                        $payment = $paymentModel->find( $form['payment_id'] );
                        $order->payment_id = $payment->getId();

                        $result['redirect'] = $order->getUrl();
                    }
                }
            } else {
                $result['error'] = 1;
                $result['errors'] = $form->getErrors();
            }
        }

        return $result;
    }

    /**
     * Fill Address from Yandex
     * @param string $address
     * @return array
     */
    private function fromYandexAddress( $address )
    {
        $yaAddress = new Sfcms\Yandex\Address();
        $yaAddress->setJsonData( $address );

        $return = array(
            'country'   => $yaAddress->country,
            'city'      => $yaAddress->city,
            'address'   => $yaAddress->getAddress(),
            'zip'       => $yaAddress->zip,
        );

        if ( $yaAddress->firstname )
            $return['fname'] = $yaAddress->firstname;
        if ( $yaAddress->lastname )
            $return['lname'] = $yaAddress->lastname;
        if ( $yaAddress->email )
            $return['email'] = $yaAddress->email;
        if ( $yaAddress->phone )
            $return['phone'] = $yaAddress->phone;
        if ( $yaAddress->comment )
            $return['comment'] = $yaAddress->comment;

        return $return;

    }

}
