<?php /* Smarty version 2.6.11, created on 2007-04-27 11:55:05
         compiled from help.tpl */ ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>

	<div id='middleColumn'>
		<div id='middleData'>

			<h2 style='text-align: center;'>HELP</h2>

			<ul>
				<li><a href='#whatisthis'>What is this thing?</a></li>
				<li><a href='#datasource'>Where does the data come from?</a></li>
				<li><a href='#searching'>What do all those search options mean?</a></li>
				<li><a href='#noresults'>Why don't my searches return any results?</a></li>
				<li><a href='#whyregister'>Why should I register?</a></li>
				<li><a href='#timestamp'>What is a diary "timestamp?"</a></li>
				<li><a href='#favorites'>What are "favorites" and how do they work?</a></li>
				<li><a href='#nofavorites'>I haven't marked anything as a favorite but things are still showing up in my favorites lists.  Why?</a></li>
				<li><a href='#favoritediary'>Where can I mark a diary as a favorite?</a></li>
			</ul>

			<a name='whatisthis' id='whatisthis' style='position: absolute;'></a>
			<p style='text-align: justify;'>
				<strong>What is this thing</strong>?<br />
				This is a searchable database of food nutrient content.  Beyond being able to do simple
				searches for foods, you are able to collect various individual foods into a "meal" and
				then view a complete nutrition profile for that "meal."  Once registered with the site,
				you will also have the ability to save food searches as well as meals so that you can
				easily and quickly reference them again later.  There is also the functionality of a food
				diary.  You may create as many diaries as you like and then add food searches or saved foods
				and meals to the diary.  This is a simple way to record what you are eating on a daily
				basis.
			</p>
			
			<a name='datasource' id='datasource' style='position: absolute;'></a>
			<p style='text-align: justify;'>
				<strong>Where does the data come from</strong>?<br />
				The USDA has done extensive testing of an impressive array of foods and food products to
				determine their respective macronutrient (protein, carbohydrate, and fat), vitamin, and
				mineral content. The data in this database was provided by the
				<a href='http://www.ars.usda.gov/ba/bhnrc/ndl'>USDA Nutrient Data Laboratory</a>.
				The data is public domain.  nubridb.com is currently using the latest release known as 
				<a href='http://www.ars.usda.gov/Services/docs.htm?docid=8964'>SR19</a>.
			</p>
			

			<a id='searching' name='searching' style='position: absolute;'></a>
			<p style='text-align: justify;'>
				<strong>What do all those search options mean</strong>?<br />
				<dl>
					<dt>All Words</dt>
						<dd>
							This is a logical 'AND' search.  This is the default search type and is probably the 
							most useful.  ALL word(s) in the search text box which are separated by any amount of 
							whitespace, MUST be present or the search will return an empty result set.  For example, 
							if you entered 'cherry pudding dessert' - the following record might be returned:<br />
							- Babyfood, dessert, cherry vanilla pudding, strained<br />
							... but the following record would not be returned because it does not contain the word 'dessert':<br />
							- Pie, cherry, prepared from recipe
						</dd>
					<dt>Any Words</dt>
						<dd>
							This a logical 'OR' search.  This search might be useful if you are having problems 
							getting any results with the 'All Words' search.  If ANY word in the search text box 
							which is separated by any amount of whitespace is found, then the record is returned as a match. 
							For example, if you entered 'banana pudding' - the following records might be returned:<br />
							- Babyfood, dessert, cherry vanilla pudding, strained<br />
							- Bananas, dehydrated, or banana powder
						</dd>
					<dt>Exact Phrase</dt>
						<dd>
							This is, as it states, an EXACT search.  This search is probably not useful at 
							all unless you are either very familiar with the database, lucky, or both.  For example, 
							if you entered 'babyfood, dessert, cherry vanilla pudding junior' - the following 
							record would NOT be returned because you are missing the comma between 'pudding' and 'junior':<br />
							- Babyfood, dessert, cherry vanilla pudding, junior<br />
						</dd>
					<dt>Partial Word</dt>
						<dd>
							Words that you enter in the search text box can constitute a portion of a word.  
							For example, if you entered 'mayo' the following record would be returned:<br />
							- Salad dressing, mayonnaise type, regular, with salt<br />
							...this is because 'mayo' makes up part of the word 'mayonnaise'.  You can see 
							that this type of search can get out of control in a hurry if you are not careful.  
							For example, if trying to be thorough you enter the text 'm', every single record 
							in the database that even contains the letter 'm' will be returned.  Although this 
							is the default search type, use it with caution or you may get more records than 
							are useful - possibly thousands.
						</dd>
					<dt>Full Word</dt>
						<dd>
							Any words that you enter in the search text box will be taken as full words in the 
							search.  For example, if you entered 'mayo'  the following record would NOT be returned:<br />
							- Salad dressing, mayonnaise type, regular, with salt<br />
							... however, this record would be returned because it contains the exact word 'Mayo':<br />
							- Salad dressing, KRAFT Mayo Light Mayonnaise
						</dd>
					<dt>Sort order</dt>
						<dd>
							This option determines how the results of your search should be ordered.  If you select
							'Category' (the default), then your results will be grouped by food category and then 
							ordered alphabetically within each group.  If you select 'Food Description' then the results
							will not be ordered, grouped, or otherwise associated with a food category.  Instead, the
							search results will simply by ordered aphabetically.
						</dd>
				</dl>
			</p>
			
			<a id='noresults' name='noresults' style='position: absolute;'></a>
			<p style='text-align: justify;'>
				<strong>Why doesn't my search return any results</strong>?<br />
				There may be several reasons why your searches are coming up empty.  Each food item in the database 
				has an associated description field which contains multiple descriptors for that particular food item.  
				When you search the database, you are simply looking for keywords in this field.  The search options 
				you choose will affect how this field is searched.  Please note that all searches are case-insensitive 
				e.g. 'a' and 'A' are treated the same.  Also, the order of words does NOT matter - a search for 'cherry vanilla'
				would return both a record that contained '...cherry...vanilla...' and '...vanilla...cherry...'.  
			</p>

			<p>
				TIP: Try pluralizing or singularizing your search terms.  A good example of this is a search for
				'blueberry.'  You may be looking for information on the fruit, yet a search for this word
				doesn't turn up any fruit.  This is because information for the fruit is registered with
				a description of "blueberries, raw."  In this case pluralizing the word would return the
				fruit, among many other things.  Another worthwhile way to search is using partial words.
				For example, a search for simply 'blueberr', assuming you are doing a "Partial Word" search,
				would return everything containing "blueberry" as well as "blueberries."
			</p>
			<a name='whyregister' id='whyregister' style='position: absolute;'></a>
			<p style='text-align: justify;'>
				</a><strong>Why should I register</strong>?<br />
				You certainly don't have to register to use this site.  However, if you don't register you will
				be unable to save anything for future reference.  Also, DRIs (similar to RDAs) are age/gender
				specific and if you are not logged in then the system will report to you the DRI values for a
				30 year old male.  Creating an account will also allow you to specify which nutrients you
				do and don't want to see by default, which may be useful as there are around 140 nutrients, most
				of which you probably won't be interested in.
			</p>
			
			<a name='timestamp' id='timestamp' style='position: absolute;'></a>
			<p style='text-align: justify;'>
				<strong>What is a diary "Timestamp"</strong>?
				A food diary "Timestamp" allows you to specify exactly when you ate a particular food or meal.
				Most people are not going to be able to enter foods into the food diary exactly at the
				moment they are eaten.  However, for a variety of reasons it is important to know when a
				particular item was consumed, especially if you are trying to determine a food sensitivity, or
				track statistics about your eating habits.  For example, with a timestamp the system may be able
				to make calculations to determine how many calories you are averaging per day, or how much
				Calcium you are intaking per week, etc.  Without a timestamp none of that would be possible.
			</p>
			
			<a name='favorites' id='favorites' style='position: absolute;'></a>
			<p style='text-align: justify;'>
				<strong>What are "favorites" and how do they work</strong>?<br />
				Marking a food or meal as a favorite will give you quick access to the items that you use most
				frequently.  For example, you may have saved 500 different foods, but perhaps you only reference,
				say, 50 or 60 of those with any frequency, so you mark those 50 or 60 foods as "Favorites."  There
				is nothing different about a favorite saved food or meal from any other saved food or meal, except
				that your favorites will be located in the quick-access drop-down menus in the left sidebar, and you
				will also have access to the "Quick Edit" functions for your favorite foods and meals.  There is no
				limit to how many foods or meals can be marked as favorite.  However, if you have many foods and
				meals you may find it inconvenient to have a drop-down menu with hundreds of items, but this is a 
				personal choice. <strong>Note</strong>: you cannot mark a diary as a favorite.  All diaries are
				considered favorites.
			</p>
			
			<a name='nofavorites' id='nofavorites' style='position: absolute;'></a>
			<p style='text-align: justify;'>
				<strong>I haven't marked anything as a favorite but things are still showing up in my favorites lists.  Why</strong>?<br />
				If your total saved foods or meals, respectively, are less than 15 then the system will automatically
				place those items in your favorites lists as a convenience.  However, only the first 15 will appear.
				For example, if you have saved 40 foods, but haven't marked any as a favorite, then the system will
				automatically select the first 15 saved foods and place them in the favorite foods drop-down menu.
				Once you mark at least one food or meal as a favorite, only those foods or meals marked as favorites
				will appear in the favorites drop-down menus, no matter how few or many. <strong>Note</strong>: you
				cannot mark a diary as a favorite.  All diaries are considered favorites.
			</p>

			<a name='favoritediary' id='favoritediary' style='position: absolute;'></a>
			<p style='text-align: justify;'>
				<strong>Where can I mark a diary as a favorite</strong>?<br />
				You can't.  It is assumed that you generally won't have a great number of diaries, as this would
				defeat the purpose of being able to summarize your nutritional trends over time.  Perhaps you will
				have only one food diary.  However, the facility of being able to have multiple diaries is present
				and may be used as one sees fit.
			</p>
		</div>
	</div>

	<div id='leftColumn'>
		<div id='leftData'>
			<?php echo $this->_tpl_vars['sidebar_left']; ?>

		</div>
	</div>

	<div id='rightColumn'>
		<div id='rightData'>
			<?php echo $this->_tpl_vars['sidebar_right']; ?>

		</div>
	</div>

</div>
<?php echo $this->_tpl_vars['footer']; ?>
 
