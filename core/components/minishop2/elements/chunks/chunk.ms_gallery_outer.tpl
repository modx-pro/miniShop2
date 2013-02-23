<div id="msGallery">
	<a rel="fancybox" href="[[++assets_url]]components/minishop2/img/web/ms2_big.png" target="_blank">
		<img src="[[++assets_url]]components/minishop2/img/web/ms2_medium.png" width="360" height="270" alt="" title="" id="mainImage" />
	</a>
	<ul class="thumbnails">
		[[+rows]]
	</ul>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		miniShop2.Gallery.initialize('#msGallery');
	})
</script>