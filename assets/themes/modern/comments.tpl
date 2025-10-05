{if $commentRows}
	<ul id="commentsList">
	{foreach $commentsArray as $commentKey => $comment}
		<li>
			{if $comment.member_id}<img src="{memberAvatar memID=$comment.member_id size=70 crop=50 hcrop=50}">{else}<img src="{$imgPath}/avatar.png" style="width: 50px;">{/if}
			<p class="commentDate">{$comment.posted}</p>
			<strong>{$comment.memberName}</strong>
			<p class="commentContent">{$comment.comment}</p>
		</li>
	{/foreach}
	</ul>
	{if $moreComments}<a href="#" class="colorLink" style="float: right; font-size: 11px;" id="showMoreComments">{$lang.showMoreComments} ({$moreComments})</a>{/if}
{else}
	<p class="notice">{$lang.noComments}</p>
{/if}
