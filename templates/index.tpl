{$header}

<div>
	<form action='' method='get' id='feedListForm' onsubmit='return confirmDelete("feedListForm");'>

		<div>
			<div class='fieldHeader' style='width: 2%; text-align: center;'>X</div>
			<div class='fieldHeader' style='width: 40%;'>URL</div>
			<div class='fieldHeader' style='width: 20%;'>User</div>
			<div class='fieldHeader' style='width: 15%;'>Feed type</div>
			<div class='fieldHeader' style='width: 15%;'>Last import</div>
		</div>
{if $feeds}
	{foreach from=$feeds item=feed}
		<div style='clear: left;' class='{cycle values="bgDark,bgLight"}'>
			<div class='fieldData' style='border-color: #ffffff; width: 2%;'><input type='radio' name='feed_id' value='{$feed.id}' /></div>
			<div class='fieldData' style='width: 40%;'><input type='text' style='width: 95%;' name='url-{$feed.id}' value='{$feed.url}' /></div>
			<div class='fieldData' style='width: 20%;'>
				<select name='user_id-{$feed.id}'>
		{foreach from=$users item=user}
			{if $user.uId == $feed.user_id}
					<option value='{$user.uId}' selected='selected'>{$user.name}</option>
			{else}
					<option value='{$user.uId}'>{$user.name}</option>
			{/if}
		{/foreach}
				</select>
			</div>
			<div class='fieldData' style='width: 15%;'>
				<select name='feed_type-{$feed.id}'>
		{foreach from=$feedTypes key=feedKey item=feedType}
			{if $feedKey == $feed.feed_type}
					<option value='{$feedKey}' selected='selected'>{$feedType}</option>
			{else}
					<option value='{$feedKey}'>{$feedType}</option>
			{/if}
		{/foreach}
				</select>
			</div>
			</div>
			<div class='fieldData' style='width: 15%; font-size: x-small;'>{$feed.last_import}</div>
		</div>
	{/foreach}
{else}
		<div style='clear: left;'>
			<strong>There are no feeds to list</strong>
		</div>
{/if}
		<div style='clear: left; margin-top: 3em;'>
			<select name='feedAction'>
				<option value='modify'>Modify</option>
				<option value='delete'>Delete</option>
			</select>
			the selected feed
			<input type='submit' name='doModifyFeed' value='Modify Feed' />
		</div>
	</form>
</div>
<div style='clear: left; margin-top: 1em;'>
	<form action='' method='post' id='addFeedForm' />
		<div>
			<div><strong>Add a new feed</strong></div>
			<div class='fieldData' style='width: 2%;'></div>
			<div class='fieldData' style='width: 40%'><input type='text' style='width: 95%;' name='url' /></div>
			<div class='fieldData' style='width: 20%;'>
				<select name='user_id'>
		{foreach from=$users item=user}
					<option value='{$user.uId}'>{$user.name}</option>
		{/foreach}
				</select>
			</div>
			<div class='fieldData' style='width: 15%;'>
				<select name='feed_type'>
		{foreach from=$feedTypes key=feedKey item=feedType}
					<option value='{$feedKey}'>{$feedType}</option>
		{/foreach}
				</select>
			</div>
		</div>
		<div>
			<input type='submit' name='doAddFeed' value='Add Feed' />
		</div>
	</form>
</div>

{$footer}
