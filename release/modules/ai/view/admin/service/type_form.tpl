{if $service_type}
    <tr>
        <td></td>
        <td>
            <div class="notice">{$service_type->getDescriptionHtml()}</div>
        </td>
    </tr>
    {$service_type->getSettingsFormHtml()}
{/if}