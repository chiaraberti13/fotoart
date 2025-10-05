<div id="footerContainer">
	<div class="center">
		{if $contentBlocks.customBlockFooter}
			<div class="cbFooter">
				<h1>{$contentBlocks.customBlockFooter.name}</h1>
				<div>{$contentBlocks.customBlockFooter.content}</div>
			</div>
		{/if}
		<div>	
			{if !addon('unbrand')}
				<!-- Powered By PhotoStore | Sell Your Photos Online -->
				<p id="poweredBy">Powered By <a href="http://www.ktools.net/photostore/" target="_blank" class="photostoreLink" title="Powered By PhotoStore | Sell Your Photos Online">PhotoStore</a><br><a href="http://www.ktools.net/photostore/" target="_blank" class="sellPhotos">Sell Photos Online</a></p>
			{/if}
			
			{if $config.settings.tospage}<a href="{linkto page='terms.of.use.php'}">{$lang.termsOfUse}</a> &nbsp;|&nbsp; {/if}
			{if $config.settings.pppage}<a href="{linkto page='privacy.policy.php'}">{$lang.privacyPolicy}</a> &nbsp;|&nbsp; {/if}
			{if $config.settings.papage}<a href="{linkto page='purchase.agreement.php'}">{$lang.purchaseAgreement}</a> {/if}
			<br>
			<p id="copyright">{$lang.copyright} <a href="{$baseURL}">{$config.settings.business_name}</a>, {$lang.reserved}</p>
		</div>
	</div>
</div>
<div id="statsCode">{$config.settings.stats_html}</div>

{if $config.settings.fotomoto}<script type="text/javascript" src="//widget.fotomoto.com/stores/script/{$config.settings.fotomoto}.js"></script>{/if}