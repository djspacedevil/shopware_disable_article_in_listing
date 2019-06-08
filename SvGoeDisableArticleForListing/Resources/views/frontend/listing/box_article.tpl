{extends file="parent:frontend/listing/box_article.tpl"}

{block name="frontend_listing_box_article_includes"}
    {if isset($sArticle.sv_goe_hide_in_listing)}
        {if $sArticle.sv_goe_hide_in_listing === '0'}
            {$smarty.block.parent}
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}