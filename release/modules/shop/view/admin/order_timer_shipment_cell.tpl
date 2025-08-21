{if $cell->getValue() == ''}-{else}
<strong class="shipment-timer" data-timestamp="{$cell->getValue()|date_format:"Y-m-d\\TH:i:sP"}"></strong>
{/if}