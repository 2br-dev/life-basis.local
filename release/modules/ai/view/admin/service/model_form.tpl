<select name="{$field->getFormName()}" {$field->getAttr()}>
    {rshtml_options options=$field->getList() selected=$field->get()}
</select>