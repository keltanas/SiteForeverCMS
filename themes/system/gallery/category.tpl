{$page.content}

<div id="siteforever_gallery_{$category.id}">
{if $rows}
    <ul class="gallery_list">
    {foreach from=$rows item="img"}
        <li>
        {if $category.target != '_none'}
            {if $category.target == '_gallery' && $img.link == ''}
                <a href="{$img.image}" class="gallery" title="{$img.name}" rel="gallery" target="_blank">
            {elseif $category.target == '_self'}
                <a {href url=$img->url}>
            {else}
                <a href="{$img.link|default:$img.image}" title="{$img.name}" target="{$category.target}">
            {/if}
        {/if}

        {thumb src=$img.image alt=$img.name width=$category.thumb_width height=$category.thumb_height method=$category.thumb_method color=$category.color}

        {if $category.target != '_none'}</a>{/if} {* _none *}

        {if $img.name}
            {if $category.target == '_self'}
                <a href="{$img->url}">{$img.name}</a>
            {else}
                <div>{$img.name}</div>
            {/if}
        {/if}
        </li>
    {/foreach}
    </ul>
    <div class="clear"></div>
{else}
    <p>Изображения не найдены</p>
{/if}
</div>

{if $paging.count}
    <p>{$paging.html}</p>
{/if}