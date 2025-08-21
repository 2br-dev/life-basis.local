    <div class="me_info">
        {t}Выбрано элементов:{/t} <strong>{$param.sel_count}</strong>
    </div>
	{if count($param.ids)==0}
		<div class="me_no_select">
            {t}Для группового редактирования необходимо отметить несколько элементов.{/t}
		</div>
	{else}

<div class="formbox" >
                            <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
            <input type="submit" value="" style="display:none">
            <div class="notabs multiedit">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            
                                    <table class="otable">
                                                                                    
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-catalog-property-item-alias" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__alias->getName()}" {if in_array($elem.__alias->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-catalog-property-item-alias">{$elem.__alias->getTitle()}</label>&nbsp;&nbsp;{if $elem.__alias->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__alias->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__alias->getRenderTemplate(true) field=$elem.__alias}</div></td>
                                </tr>
                                                            
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-catalog-property-item-description" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__description->getName()}" {if in_array($elem.__description->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-catalog-property-item-description">{$elem.__description->getTitle()}</label>&nbsp;&nbsp;{if $elem.__description->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__description->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__description->getRenderTemplate(true) field=$elem.__description}</div></td>
                                </tr>
                                                            
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-catalog-property-item-unit" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__unit->getName()}" {if in_array($elem.__unit->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-catalog-property-item-unit">{$elem.__unit->getTitle()}</label>&nbsp;&nbsp;{if $elem.__unit->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__unit->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__unit->getRenderTemplate(true) field=$elem.__unit}</div></td>
                                </tr>
                                                            
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-catalog-property-item-unit_export" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__unit_export->getName()}" {if in_array($elem.__unit_export->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-catalog-property-item-unit_export">{$elem.__unit_export->getTitle()}</label>&nbsp;&nbsp;{if $elem.__unit_export->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__unit_export->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__unit_export->getRenderTemplate(true) field=$elem.__unit_export}</div></td>
                                </tr>
                                                            
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-catalog-property-item-name_for_export" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__name_for_export->getName()}" {if in_array($elem.__name_for_export->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-catalog-property-item-name_for_export">{$elem.__name_for_export->getTitle()}</label>&nbsp;&nbsp;{if $elem.__name_for_export->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__name_for_export->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__name_for_export->getRenderTemplate(true) field=$elem.__name_for_export}</div></td>
                                </tr>
                                                            
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-catalog-property-item-xml_id" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__xml_id->getName()}" {if in_array($elem.__xml_id->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-catalog-property-item-xml_id">{$elem.__xml_id->getTitle()}</label>&nbsp;&nbsp;{if $elem.__xml_id->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__xml_id->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__xml_id->getRenderTemplate(true) field=$elem.__xml_id}</div></td>
                                </tr>
                                                            
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-catalog-property-item-parent_id" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__parent_id->getName()}" {if in_array($elem.__parent_id->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-catalog-property-item-parent_id">{$elem.__parent_id->getTitle()}</label>&nbsp;&nbsp;{if $elem.__parent_id->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__parent_id->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__parent_id->getRenderTemplate(true) field=$elem.__parent_id}</div></td>
                                </tr>
                                                            
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-catalog-property-item-int_hide_inputs" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__int_hide_inputs->getName()}" {if in_array($elem.__int_hide_inputs->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-catalog-property-item-int_hide_inputs">{$elem.__int_hide_inputs->getTitle()}</label>&nbsp;&nbsp;{if $elem.__int_hide_inputs->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__int_hide_inputs->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__int_hide_inputs->getRenderTemplate(true) field=$elem.__int_hide_inputs}</div></td>
                                </tr>
                                                            
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-catalog-property-item-hidden" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__hidden->getName()}" {if in_array($elem.__hidden->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-catalog-property-item-hidden">{$elem.__hidden->getTitle()}</label>&nbsp;&nbsp;{if $elem.__hidden->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__hidden->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__hidden->getRenderTemplate(true) field=$elem.__hidden}</div></td>
                                </tr>
                                                            
                                <tr class="editrow">
                                    <td class="ochk" width="20">
                                        <input id="me-catalog-property-item-no_export" title="{t}Отметьте, чтобы применить изменения по этому полю{/t}" type="checkbox" class="doedit" name="doedit[]" value="{$elem.__no_export->getName()}" {if in_array($elem.__no_export->getName(), $param.doedit)}checked{/if}></td>
                                    <td class="otitle">
                                        <label for="me-catalog-property-item-no_export">{$elem.__no_export->getTitle()}</label>&nbsp;&nbsp;{if $elem.__no_export->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__no_export->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td><div class="multi_edit_rightcol coveron"><div class="cover"></div>{include file=$elem.__no_export->getRenderTemplate(true) field=$elem.__no_export}</div></td>
                                </tr>
                                                                        </table>
                            </div>
        </form>
    </div>
{/if}