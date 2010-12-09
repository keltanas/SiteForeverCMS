{if $cat.show_content}
    {$page.content}
{/if}

{if $cat.show_list}
    {foreach from=$list item="item"}
    <div>
        <div><strong>{$item.date|date_format:"%x"}</strong></div>
        <p><a {href url=$item.link doc=$item.id}>{$item.title}</a></p>
        <div>{$item.notice}</div>
        <div class="right"><a {href url="news" doc=$item.id}>подробнее...</a></div>
    </div>
    {foreachelse}
    <div>
        В этом разделе пока нет материалов
    </div>
    {/foreach}

    <hr />
    {$paging.html}
{/if}