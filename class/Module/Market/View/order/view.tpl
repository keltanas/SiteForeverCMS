<h3>{$order->emptorName}, Ваш заказ №{$order->id}</h3>

{*<h4>Вы готовы заказать товары из корзины?</h4>*}

<table class="table">
    <tr>
        <th></th>
        <th>Наименование</th>
        <th>Детали</th>
        <th>Количество</th>
    </tr>
{foreach from=$positions item="pos"}
    <tr>
        <td>
            {thumb src=$pos->Product->image width="100" height="auto"}
        </td>
        <td>
            {a href=$pos->Product->url}{$pos->Product->name}{/a}
        </td>
        <td>
            <i>{$pos.details}</i>
        </td>
        <td>
            {$pos.count} шт.<br>по {$pos.price} р.
        </td>
    </tr>
{/foreach}
</table>

{if $delivery}
<h4>{t cat="delivery"}Delivery{/t}</h4>
<ul>
    <li>Способ доставки: {$delivery->getObject()->name}</li>
    <li>Адрес доставки: {$order->address}</li>
    <li>Стоимость: {$delivery->cost()} Р.</li>
</ul>
{/if}

<h3>Итого: {$sum} р.</h3>

{if $payment}
<h4>{t}Payment{/t}</h4>
<p>Способ оплаты: {$payment->name}</p>
    {if $order->paid}
        <p>Оплачено!</p>
    {else}
        {if $robokassa}{*
            *}<p><a class="btn btn-success" href="{$robokassa->getLink(true)}">Перейти к оплате</a></p>{*
        *}{/if}
        <p>В ближайшее время наш менеджер свяжется с Вами.</p>
    {/if}
{/if}

{if $this->auth->isLogged()}
<hr>
<p>
    {a href="order" complete="yes" class="btn"}Мои заказы{/a}
    {a href="basket" class="btn"}Моя корзина{/a}
</p>
{/if}
