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
                                    <td class="otitle">{$elem.__link->getTitle()}&nbsp;&nbsp;{if $elem.__link->getHint() != ''}<a class="help-icon" title="{$elem.__link->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__link->getRenderTemplate() field=$elem.__link}</td>
                                </tr>
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__width->getTitle()}&nbsp;&nbsp;{if $elem.__width->getHint() != ''}<a class="help-icon" title="{$elem.__width->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__width->getRenderTemplate() field=$elem.__width}</td>
                                </tr>
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__height->getTitle()}&nbsp;&nbsp;{if $elem.__height->getHint() != ''}<a class="help-icon" title="{$elem.__height->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__height->getRenderTemplate() field=$elem.__height}</td>
                                </tr>
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__logo_type->getTitle()}&nbsp;&nbsp;{if $elem.__logo_type->getHint() != ''}<a class="help-icon" title="{$elem.__logo_type->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__logo_type->getRenderTemplate() field=$elem.__logo_type}</td>
                                </tr>
                                                                                                        </table>
                            </div>
        </form>
    </div>