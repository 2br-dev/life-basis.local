                                            
                                            
    
                                    
            <tr>
                <td class="otitle">{$elem.__link->getTitle()}&nbsp;&nbsp;{if $elem.__link->getHint() != ''}<a class="help-icon" title="{$elem.__link->getHint()|escape}">?</a>{/if}
                </td>
                <td>{include file=$elem.__link->getRenderTemplate() field=$elem.__link}</td>
            </tr>
                                            
            <tr>
                <td class="otitle">{$elem.__target_blank->getTitle()}&nbsp;&nbsp;{if $elem.__target_blank->getHint() != ''}<a class="help-icon" title="{$elem.__target_blank->getHint()|escape}">?</a>{/if}
                </td>
                <td>{include file=$elem.__target_blank->getRenderTemplate() field=$elem.__target_blank}</td>
            </tr>
                        