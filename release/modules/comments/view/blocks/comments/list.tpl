{addcss file="%comments%/comments.css"}

{foreach $commentlist as $comment}
    <div class="product-review-item" {$comment->getDebugAttributes()}>
        <div class="row g-3">
            <div class="col">
                <div class="product-review-item__title">{$comment.user_name}</div>
                <div class="fs-5 text-gray">{$comment.dateof|dateformat:"@date @time"}</div>
            </div>
            <div class="col-auto">
                <div class="rating-stars">
                    <div class="rating-stars__act" style="width: {$comment.rate*20}%;"></div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            {$comment.message|nl2br}
        </div>
        {$photos = $comment->getPhotos()}
        {if $config.view_attachments && !empty($photos)}
            <div class="photo-container swiper-container">
                <div class="photo-list swiper-wrapper" id="photo-list">
                    {foreach $photos as $photo}
                        <div class="one-photo-container swiper-slide">
                            <a class="one-photo" href="{$photo->getUrl(1000, 1000, 'xy')}">
                                <img src="{$photo->getUrl(180, 180, 'xy')}"
                                 alt="{$image.title|default:"{t title=$photo.title n=$image@iteration}%title фото %n{/t}"}">
                            </a>
                        </div>
                    {/foreach}
                </div>
                <!-- Навигация "вперед" и "назад" -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        {/if}
    </div>
    {if $comment.answer_message != ''}
        <div class="product-review-answer">
            <div class="row">
                <div class="col">
                    <div class="product-review-answer__title">{t}Ответ магазина{/t}</div>
                    <div class="fs-5 text-gray">{$comment.answer_dateof|dateformat:"@date @time"}</div>
                </div>
            </div>
            <div class="mt-3">
                {$comment.answer_message|nl2br}
            </div>

        </div>
    {/if}
{/foreach}