<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<style>
    :root {
        --swiper-theme-color: #9c9c9c;
    }
    .swiper {
        width: 100%;
        height: 100%;
        background-color: #dedede;
    }

    .swiper-slide {
        text-align: center;
        font-size: 18px;

        /* Center slide text vertically */
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
    }

    .swiper-slide img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .swiper {
        width: 100%;
        height: 300px;
        margin-left: auto;
        margin-right: auto;
    }

    .swiper-slide {
        background-size: cover;
        background-position: center;
    }

    .mySwiper2 {
        height: 80%;
        width: 100%;
    }
    .mySwiper2 .swiper-slide {
        padding: 1px 1px 0 1px;
    }

    .mySwiper {
        height: 20%;
        box-sizing: border-box;
        padding: 1px;
    }

    .mySwiper .swiper-slide {
        width: 25%;
        height: 100%;
        opacity: 0.7;
    }

    .mySwiper .swiper-slide-thumb-active {
        opacity: 1;
    }

    .swiper-slide img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div id="msGallery">
    {if $files?}
        {set $slides = ''}
        {foreach $files as $file}
            {set $slides = $slides ~ '<div class="swiper-slide"><img src="'~$file['small']~'" alt="'~$file['description']~'" title="'~$file['name']~'"></div>'}
        {/foreach}
        <div class="swiper mySwiper2">
            <div class="swiper-wrapper">
                {$slides}
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
               {$slides}
            </div>
        </div>
    {else}
        <img src="{('assets_url' | option) ~ 'components/minishop2/img/web/ms2_medium.png'}"
             srcset="{('assets_url' | option) ~ 'components/minishop2/img/web/ms2_medium@2x.png'} 2x"
             alt="" title=""/>
    {/if}
</div>

<script>
    var swiper = new Swiper(".mySwiper", {
        loop: true,
        spaceBetween: 1,
        slidesPerView: 4,
        freeMode: true,
        watchSlidesProgress: true,
    });
    var swiper2 = new Swiper(".mySwiper2", {
        loop: true,
        spaceBetween: 1,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        thumbs: {
            swiper: swiper,
        },
    });
</script>
