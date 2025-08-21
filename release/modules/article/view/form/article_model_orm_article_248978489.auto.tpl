<div class="formbox" >
                <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                    <li class=" active"><a data-target="#article-article-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                    <li class=""><a data-target="#article-article-tab1" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(1)}</a></li>
                    <li class=""><a data-target="#article-article-tab2" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(2)}</a></li>
                    <li class=""><a data-target="#article-article-tab3" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(3)}</a></li>
                    <li class=""><a data-target="#article-article-tab4" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(4)}</a></li>
                    <li class=""><a data-target="#article-article-tab5" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(5)}</a></li>
                    <li class=""><a data-target="#article-article-tab6" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(6)}</a></li>
                </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none"/>
                        <div class="tab-pane active" id="article-article-tab0" role="tabpanel">
                                                                                                                                    {include file=$elem.__id->getRenderTemplate() field=$elem.__id}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <table class="otable">
                                                                                                                                                        
                                <tr>
                                    <td class="otitle">{$elem.__title->getTitle()}&nbsp;&nbsp;{if $elem.__title->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__title->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__title->getRenderTemplate() field=$elem.__title}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__alias->getTitle()}&nbsp;&nbsp;{if $elem.__alias->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__alias->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__alias->getRenderTemplate() field=$elem.__alias}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__content->getTitle()}&nbsp;&nbsp;{if $elem.__content->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__content->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__content->getRenderTemplate() field=$elem.__content}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__parent->getTitle()}&nbsp;&nbsp;{if $elem.__parent->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__parent->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__parent->getRenderTemplate() field=$elem.__parent}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__dateof->getTitle()}&nbsp;&nbsp;{if $elem.__dateof->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dateof->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__dateof->getRenderTemplate() field=$elem.__dateof}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__dont_show_before_date->getTitle()}&nbsp;&nbsp;{if $elem.__dont_show_before_date->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__dont_show_before_date->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__dont_show_before_date->getRenderTemplate() field=$elem.__dont_show_before_date}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__image->getTitle()}&nbsp;&nbsp;{if $elem.__image->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__image->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__image->getRenderTemplate() field=$elem.__image}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__user_id->getTitle()}&nbsp;&nbsp;{if $elem.__user_id->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__user_id->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__user_id->getRenderTemplate() field=$elem.__user_id}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__public->getTitle()}&nbsp;&nbsp;{if $elem.__public->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__public->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__public->getRenderTemplate() field=$elem.__public}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="article-article-tab1" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__short_content->getTitle()}&nbsp;&nbsp;{if $elem.__short_content->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__short_content->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__short_content->getRenderTemplate() field=$elem.__short_content}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="article-article-tab2" role="tabpanel">
                                                                                                                                                                                                                                                                                                                <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__meta_title->getTitle()}&nbsp;&nbsp;{if $elem.__meta_title->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__meta_title->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__meta_title->getRenderTemplate() field=$elem.__meta_title}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__meta_keywords->getTitle()}&nbsp;&nbsp;{if $elem.__meta_keywords->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__meta_keywords->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__meta_keywords->getRenderTemplate() field=$elem.__meta_keywords}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__meta_description->getTitle()}&nbsp;&nbsp;{if $elem.__meta_description->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__meta_description->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__meta_description->getRenderTemplate() field=$elem.__meta_description}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="article-article-tab3" role="tabpanel">
                                                                                                            {include file=$elem.___photo_->getRenderTemplate() field=$elem.___photo_}
                                                                                                                                                </div>
                        <div class="tab-pane" id="article-article-tab4" role="tabpanel">
                                                                                                            {include file=$elem.___attached_products_->getRenderTemplate() field=$elem.___attached_products_}
                                                                                                                                                </div>
                        <div class="tab-pane" id="article-article-tab5" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__affiliate_id->getTitle()}&nbsp;&nbsp;{if $elem.__affiliate_id->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__affiliate_id->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__affiliate_id->getRenderTemplate() field=$elem.__affiliate_id}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="article-article-tab6" role="tabpanel">
                                                                                                            {include file=$elem.___tags_->getRenderTemplate() field=$elem.___tags_}
                                                                                                                                                </div>
                    </form>
    </div>
    </div>