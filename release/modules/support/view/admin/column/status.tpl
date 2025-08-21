{addcss file="%support%/support_admin.css"}
{$status = $cell->getRow()->getStatus()}
<div class="ticket-number f-12" style="background: {$status.background}">{$cell->getValue()}</div>