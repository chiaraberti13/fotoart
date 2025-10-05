<footer>
	{if $contentBlocks.customBlockFooter}
		<div>{$contentBlocks.customBlockFooter.content}</div>
	{/if}
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				{$lang.copyright} <a href="{$baseURL}">{$config.settings.business_name}</a><br>{$lang.reserved}
			</div>
			<div class="col-md-3">
				{if addon('rss')}
				<ul>
				{* 	<li><strong>{$lang.rss}</strong></li> *}
					{if $config.settings.rss_newest}<li><a href="{linkto page='rss.php?mode=newestMedia'}">{$lang.newestMedia}</a></li>{/if}
					{if $config.settings.rss_newest}<li><a href="{linkto page='rss.php?mode=popularMedia'}">{$lang.popularMedia}</a></li>{/if}
					{if $config.settings.rss_featured_media}<li><a href="{linkto page='rss.php?mode=featuredMedia'}">{$lang.featuredMedia}</a></li>{/if}
				</ul>
				{/if}
			</div>
			<div class="col-md-3">
				<ul style="margin-bottom: 10px;">
					{if $config.settings.contact}<li><a href="{linkto page="contact.php"}">{$lang.contactUs}</a></li>{/if}
					{if $config.settings.aboutpage}<li><a href="{linkto page="about.php"}">{$lang.aboutUs}</a></li>{/if}
					{if $config.settings.forum_link}<li><a href="{$config.settings.forum_link}">{$lang.forum}</a></li>{/if}					
					{if $config.settings.tospage}<li><a href="{linkto page='terms.of.use.php'}">{$lang.termsOfUse}</a></li>{/if}
					{if $config.settings.pppage}<li><a href="{linkto page='privacy.policy.php'}">{$lang.privacyPolicy}</a></li>{/if}
					{if $config.settings.papage}<li><a href="{linkto page='purchase.agreement.php'}">{$lang.purchaseAgreement}</a></li>{/if}
				</ul>
				{if $config.settings.facebook_link}<a href="{$config.settings.facebook_link}" target="_blank"><img src="{$imgPath}/facebook.icon.png" width="20" title="Facebook"></a>{/if}&nbsp;{if $config.settings.twitter_link}<a href="{$config.settings.twitter_link}" target="_blank"><img src="{$imgPath}/twitter.icon.png" width="20" title="Twitter"></a>{/if}
			</div>
			<div class="col-md-3">
				<ul style="margin-bottom: 10px;">
					<li><a href="{linkto page="content.php?id=4"}">{$lang.footercfunction}</a></li>
				{* 	<li><a href="{linkto page="content.php?id=89"}">{$lang.footerinfoart}</a></li> *}
					<li><a href="{linkto page="content.php?id=77"}">{$lang.footerinfofoto}</a></li>
					<li><a href="{linkto page="content.php?id=24"}">{$lang.footerfaq}</a></li>
				{*	<li><a href="{linkto page="missing_part.php"}">{$lang.footermissing}</a></li> *}
					
				</ul>
				{if $config.settings.facebook_link}<a href="{$config.settings.facebook_link}" target="_blank"><img src="{$imgPath}/facebook.icon.png" width="20" title="Facebook"></a>{/if}&nbsp;{if $config.settings.twitter_link}<a href="{$config.settings.twitter_link}" target="_blank"><img src="{$imgPath}/twitter.icon.png" width="20" title="Twitter"></a>{/if}
			</div>
			<div class="col-md-3 text-right">
				{if !addon('unbrand')}
					<!-- Powered By PhotoStore | Sell Your Photos Online -->
					<p id="poweredBy">Powered By <a href="http://www.ktools.net/photostore/" target="_blank" class="photostoreLink" title="Powered By PhotoStore | Sell Your Photos Online">PhotoStore</a><br><a href="http://www.ktools.net/photostore/" target="_blank" class="sellPhotos">Sell Photos Online</a></p>
				{/if}
			</div>
		</div>
	</div>
	<div id="statsCode">{$config.settings.stats_html}</div>
</footer>
{if $pageID != 'photoPuzzle'}
	<script src="{$baseURL}/assets/themes/{$theme}/js/bootstrap.min.js"></script>
{else}
	<script src="{$baseURL}/assets/javascript/bootstrap.min.js"></script>
    <script src="{$baseURL}/assets/javascript/photo.puzzle.js"></script>
    <script src="{$baseURL}/assets/javascript/jquery.Jcrop.js"></script>
    <script src="{$baseURL}/assets/javascript/dmuploader.min.js"></script>
{/if}
{if $config.settings.fotomoto}<script type="text/javascript" src="//widget.fotomoto.com/stores/script/{$config.settings.fotomoto}.js"></script>{/if}