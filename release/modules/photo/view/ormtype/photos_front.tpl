{addjs file="%files%/libs/dropzone.min.js"}
{addcss file="%files%/libs/dropzone.css"}
{addjs file="%files%/fileupload.js"}
{$link_object = $field->getLinkObject()}
<div class="dropzone rs-dropzone"
     data-upload-url="{$field->getFrontUploadUrl()}"
     data-remove-url="{$field->getFrontRemoveUrl()}"
     data-input-name="{$field->getName()}"
     data-accepted-files="{$field->getAcceptExtensions(',')}"
     data-max-filesize-mb="{$link_object->getMaxFileSize('m')}"
     data-dropzone-options='{$field->getDropZoneOptions(true)}'>
</div>
<div class="dropzone-preview">
    {foreach $field->getDraftFiles() as $photo}
        <div class="dz-preview dz-file-preview" data-public-hash="{$photo.uniq}">
            <div class="dz-left-part">
                <div class="dz-size"><span data-dz-size="">{$photo.size|format_filesize}</div>
            </div>
            <a class="dz-remove" data-remove-file></a>
            <input type="hidden" name="{$field->getName()}[]" value="{$photo.uniq}">
        </div>
    {/foreach}
</div>