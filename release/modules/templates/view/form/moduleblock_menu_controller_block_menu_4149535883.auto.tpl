<div class="formbox" >
                        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
            <input type="submit" value="" style="display:none">
            <div class="notabs">
                                                                                                            
                                                                                            
                                                    
                                    <table class="otable">
                                                                                                                    
                                <tr>
                                    <td class="otitle">{$elem.__indexTemplate->getTitle()}&nbsp;&nbsp;{if $elem.__indexTemplate->getHint() != ''}<a class="help-icon" title="{$elem.__indexTemplate->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__indexTemplate->getRenderTemplate() field=$elem.__indexTemplate}</td>
                                </tr>
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__root->getTitle()}&nbsp;&nbsp;{if $elem.__root->getHint() != ''}<a class="help-icon" title="{$elem.__root->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__root->getRenderTemplate() field=$elem.__root}</td>
                                </tr>
                                                                                                        </table>
                            </div>
        </form>
    </div>