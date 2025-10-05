<div class="divTableRow">
	<div class="divTableCell formFieldLabel">{$optionGroup.name}<!-- option{$prefix}[{$key}]}-->:</div>
	<div class="divTableCell">
	{if $optionGroup.ltype == 1}
		<select id="option{$prefix}-{$key}" name="option{$prefix}[{$key}]" {if $optionGroup.required}require="require"{/if} errorMessage="{$lang.required}" class="form-control">
			<option value="0" price="0" credits="0"></option>
			{foreach $optionGroup.options as $key2 => $option}	
				<option 
					value="{$option.op_id}" 
					price="{if $option.price}{if $option.priceMod == '-'}-{/if}{$option.nativePrice}{/if}" 
					credits="{if $option.credits}{if $option.creditsMod == '-'}-{/if}{$option.credits}{/if}" 
					{if $option.selected}selected="selected"{/if}
				>{$option.name} {if $option.price}({$option.priceMod} {$option.price.display}{if $option.price.taxInc} {$lang.taxIncMessage}{/if}){/if} {if $option.credits}({$option.creditsMod} {$option.credits} {$lang.mediaLabelCredits}){/if}</option>
			{/foreach}
		</select>
	{/if}
	
	{if $optionGroup.ltype == 2}
		{foreach $optionGroup.options as $key2 => $option}	
			<input
				type="checkbox" 
				name="option{$prefix}[{$key}-{$option.op_id}]" 
				id="option-{$prefix}-{$key}-{$option.op_id}" 
				price="{if $option.price}{if $option.priceMod == '-'}-{/if}{$option.nativePrice}{/if}" 
				credits="{if $option.credits}{if $option.creditsMod == '-'}-{/if}{$option.credits}{/if}" 
				value="1" 
				errorMessage="{$lang.required}" 
				{if $option.selected}checked="checked" {/if}
				class="form-control"
			/> <label for="option-{$prefix}-{$key}-{$option.op_id}">{$option.name}<!-- - option-{$prefix}-{$key}-{$option.op_id}--> {if $option.price}<span class="price">({$option.priceMod} {$option.price.display})</span>{if $option.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if} {if $option.credits}<span class="price">({$option.creditsMod} {$option.credits} {$lang.mediaLabelCredits})</span>{/if}</label><br>
		{/foreach}
	{/if}
	
	{if $optionGroup.ltype == 3}
		<div class="radioGroup">
		<input type="radio" name="option{$prefix}[{$key}]" id="option{$prefix}-{$key}-none" price="0" credits="0" value="0" {if $optionGroup.required}require="require"{/if} errorMessage="{$lang.required}"> <label for="option{$prefix}-{$key}-none">{$lang.none}</label><br>
		{foreach $optionGroup.options as $key2 => $option}	
			<input 
				type="radio" 
				name="option{$prefix}[{$key}]" 
				id="option{$prefix}-{$key}-{$option.op_id}" 
				price="{if $option.price}{if $option.priceMod == '-'}-{/if}{$option.nativePrice}{/if}" 
				credits="{if $option.credits}{if $option.creditsMod == '-'}-{/if}{$option.credits}{/if}" 
				{if $optionGroup.required}require="require"{/if} 
				value="{$option.op_id}" 
				{if $option.selected}checked="checked"{/if} 
				errorMessage="{$lang.required}" 
				class=""
			/> <label for="option{$prefix}-{$key}-{$option.op_id}">{$option.name}<!-- +++ option{$prefix}-{$key}-{$option.op_id}--></label> {if $option.price}<span class="price">({$option.priceMod} {$option.price.display})</span>{if $option.price.taxInc} <span class="taxIncMessage">({$lang.taxIncMessage})</span>{/if}{/if} {if $option.credits}<span class="price">({$option.creditsMod} {$option.credits} {$lang.mediaLabelCredits})</span>{/if}<br>
		{/foreach}
		</div>
	{/if}
	</div>
</div>