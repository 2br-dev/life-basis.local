<div class="formbox" >
                        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
            <input type="submit" value="" style="display:none">
            <div class="notabs">
                                                                                                            
                                                    
                                    <table class="otable">
                                                                                                                    
                                <tr>
                                    <td class="otitle">{$elem.__grid_system->getTitle()}&nbsp;&nbsp;{if $elem.__grid_system->getHint() != ''}<a class="help-icon" title="{$elem.__grid_system->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__grid_system->getRenderTemplate() field=$elem.__grid_system}</td>
                                </tr>
                                                                                                        </table>
                            </div>
        </form>
    </div>