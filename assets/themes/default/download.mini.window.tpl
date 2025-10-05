{if $digitalRows}
	<script>
		registerWorkboxLinks();
		// Only 1 row exists - automatically open workbox
		
		if({$digitalRows} == 1)
		{
			{foreach $digitalsArray as $digital}
				workbox({ page : '{$digital.linkto}' });
				$('.thumbDownloadContainer').hide();
			{/foreach}
		}
		else
		{
			$('#thumbDownloadContainer'+'{$mediaID}').show();	
		}
	</script>
	{if $digitalRows > 1}
		<ul>
			{foreach $digitalsArray as $digital}
				<li href="{$digital.linkto}" class="workboxLinkAttach workboxLink">{$digital.name}{if $digital.dsp_type == 'video' and $digital.hd}<img src="{$imgPath}/hd.png" style="vertical-align:top;">{/if}</li>
			{/foreach}
		</ul>
	{/if}
{else}
	<script>
		$('#thumbDownloadContainer'+'{$mediaID}').show();
	</script>
	<p style="padding: 10px;">{*$digitalRows*}{$lang.noOptionsAvailable}</p>
{/if}