{addjs file="%files%/libs/dropzone.min.js"}
{addcss file="%files%/libs/dropzone.css"}
{addjs file="%files%/fileupload.js"}

{$link_object = $field->getLinkObject()}
<div class="dropzone rs-dropzone"
     data-upload-url="{$field->getFrontUploadUrl()}"
     data-remove-url="{$field->getFrontRemoveUrl()}"
     data-input-name="{$field->getName()}"
     data-max-filesize-mb="{$link_object->getMaxFileSize('m')}"
     data-accepted-files="{$link_object->getAllowExtensionsString()}"
     data-dropzone-options='{$field->getDropZoneOptions(true)}'
     {$field->getAttr()}>
</div>
<div class="dropzone-preview">
    {foreach $field->getDraftFiles() as $file}
        <div class="dz-preview dz-file-preview" data-public-hash="{$file.uniq}">
            <div class="dz-left-part">
                <div class="dz-filename"><a data-dz-name="" href="{$file->getHashedUrl()}" target="_blank">{$file.name}</a></div>
                <div class="dz-size"><span data-dz-size="">{$file.size|format_filesize}</div>
            </div>
            <a class="dz-remove" data-remove-file></a>
            <input type="hidden" name="{$field->getName()}[]" value="{$file.uniq}">
        </div>
    {/foreach}
</div>