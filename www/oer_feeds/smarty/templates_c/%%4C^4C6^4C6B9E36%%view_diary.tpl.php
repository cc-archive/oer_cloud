<?php /* Smarty version 2.6.11, created on 2007-05-08 22:21:31
         compiled from view_diary.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', 'view_diary.tpl', 48, false),array('function', 'cycle', 'view_diary.tpl', 83, false),)), $this); ?>
<?php echo $this->_tpl_vars['header']; ?>

<div id='columnContainer'>
	<div id='middleColumn'>
		<div id='middleData'>
			<div class='standardMargins'>
				<strong>Diary</strong>: <?php echo $this->_tpl_vars['diaryDesc']; ?>

			</div>

			<div id='diaryCalendar' style='width: 15em; margin-bottom: 2ex; margin-right: 2ex; float: left;'></div>
			<script type='text/javascript'>
<?php echo '
				function dateChanged(calendar) {
					if (calendar.dateClicked) {
						var y = calendar.date.getFullYear();
						var m = calendar.date.getMonth();
						var d = calendar.date.getDate();
'; ?>

						window.location = "view_diary.php?diary=<?php echo $_GET['diary']; ?>
&action=viewDiary&date=" + y + "-" + (m + 1) + "-" + d;
<?php echo '
					}
				}
				Calendar.setup(
					{
						flat         : "diaryCalendar", // ID of the parent element
						flatCallback : dateChanged,           // our callback function
'; ?>

						range : [<?php echo $this->_tpl_vars['firstYear']; ?>
,<?php echo $this->_tpl_vars['lastYear']; ?>
],
						date : "<?php echo $this->_tpl_vars['calendarStartDate']; ?>
"
<?php echo '
					}
				);
'; ?>

			</script>

			<div style='float: left;'>
				<div style='margin-bottom: 2ex;'>
					Navigate this diary with the calendar.
				</div>
				<form action='edit_diary.php' method='post' id='formEditDiary' onsubmit='return validateAddDiaryNote("formEditDiary");'>
					<div><strong>Add a note to this diary:</strong></div>
					<textarea name='note' rows='3' style='width: 97%;'></textarea>
					<div style='margin-bottom: .5em;'>
						<input type='hidden' name='action' value='addNote' />
						<input type='hidden' name='diary' value='<?php echo $_GET['diary']; ?>
' />
						<input type='submit' name='doAddNote' value='Add note' />
						with
						<a href='help.php#timestamp' title='What is a diary timestamp?'>timestamp</a>
						<input type='text' name='diaryTimestamp' id='diaryTimestamp' readonly='readonly' value='<?php echo ((is_array($_tmp=$_GET['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y-%m-%d") : smarty_modifier_date_format($_tmp, "%Y-%m-%d")); ?>
' size='20' />
						<script type="text/javascript">
							Calendar.setup(
								<?php echo '{'; ?>

									inputField	: "diaryTimestamp",
									ifFormat	: "%Y-%m-%d %I:%M%p",
									button		: "diaryTimestamp",
									weekNumbers	: false,
									showsTime	: true,
									firstDay	: 0
								<?php echo '}'; ?>

							);
						</script>
					</div>
					<div>
						<small>TIP: leave the timestamp blank to use the present date/time.</small>
					</div>
				</form>
			</div>
			<div style='clear: both;'></div>
			
<?php if ($this->_tpl_vars['diaryItems']): ?>
			<div class='standardMargins'>
				<strong>Diary entries for <?php echo ((is_array($_tmp=$_GET['date'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%A, %B %e, %Y") : smarty_modifier_date_format($_tmp, "%A, %B %e, %Y")); ?>
.</strong><br />
			</div>

			<div class='standardMargins' style='width: 100%;'>
				<table class='standardTable'>
					<tr class='tableTitleRow'>
						<th>Item</th>
						<th>Date</th>
						<th>Type</th>
						<th>X</th>
					</tr>
	<?php $_from = $this->_tpl_vars['diaryItems']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['diaryItem']):
?>
					<tr class='<?php echo smarty_function_cycle(array('values' => "bgDark,bgLight"), $this);?>
' id='itemRow-<?php echo $this->_tpl_vars['diaryItem']['id']; ?>
'>
		<?php if ($this->_tpl_vars['diaryItem']['type'] == 'Food'): ?>
						<td><a href='view_food.php?<?php echo $this->_tpl_vars['diaryItem']['uri']; ?>
' id='itemDesc-<?php echo $this->_tpl_vars['diaryItem']['id']; ?>
'><?php echo $this->_tpl_vars['diaryItem']['description']; ?>
</a></td>
		<?php elseif ($this->_tpl_vars['diaryItem']['type'] == 'Meal'): ?>
						<td><a href='view_meal.php?<?php echo $this->_tpl_vars['diaryItem']['uri']; ?>
' id='itemDesc-<?php echo $this->_tpl_vars['diaryItem']['id']; ?>
'><?php echo $this->_tpl_vars['diaryItem']['description']; ?>
</a></td>
		<?php elseif ($this->_tpl_vars['diaryItem']['type'] == 'Note'): ?>
						<td><span id='itemDesc-<?php echo $this->_tpl_vars['diaryItem']['id']; ?>
'><?php echo $this->_tpl_vars['diaryItem']['data']; ?>
</span></td>
		<?php endif; ?>
						<td style='text-align: center;'><?php echo $this->_tpl_vars['diaryItem']['date']; ?>
</td>
						<td style='text-align: center;'><?php echo $this->_tpl_vars['diaryItem']['type']; ?>
</td>
						<td style='text-align: center;'>
							<a href='<?php echo $_SERVER['REQUEST_URI']; ?>
' onclick='verifyRemoveDiaryItem("<?php echo $this->_tpl_vars['diaryItem']['id']; ?>
"); return false;'>
								<img src='<?php echo $this->_tpl_vars['config']->_imgUri; ?>
/remove.png' alt='Del' title='Remove: <?php echo $this->_tpl_vars['diaryItem']['description']; ?>
' />
							</a>
						</td>
					</tr>
	<?php endforeach; endif; unset($_from); ?>
				</table>
			</div>

	<?php if ($this->_tpl_vars['summaryData']): ?>
			<div class='standardMargins' style='width: 100%'>
				<strong>Nutrient summary.</strong><br />
		<?php if ($this->_tpl_vars['showAllNutrients']): ?>
				<a href='<?php echo $this->_tpl_vars['config']->_rootUri; ?>
/<?php echo $this->_tpl_vars['config']->_thisScript; ?>
?diary=<?php echo $_GET['diary']; ?>
&action=<?php echo $_GET['action']; ?>
&date=<?php echo $_GET['date']; ?>
'>
				    Hide unwanted nutrients</a> |
		<?php else: ?>
				<a href='<?php echo $this->_tpl_vars['config']->_rootUri; ?>
/<?php echo $this->_tpl_vars['config']->_thisScript; ?>
?diary=<?php echo $_GET['diary']; ?>
&action=<?php echo $_GET['action']; ?>
&date=<?php echo $_GET['date']; ?>
&showall'>
					Show all nutrients</a> |
		<?php endif; ?>
				<a href='nutrient_chooser.php'>Manage nutrient list</a>
			</div>

			<div class='standardMargins' style='width: 100%;'>
				<table class='standardTable'>
					<tr class='tableTitleRow'>
						<th>Nutrient</th>
						<th>Total</th>
						<th>%DRI</th>
					</tr>
		<?php $_from = $this->_tpl_vars['summaryData']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['nutrient']):
?>
					<tr class='<?php echo smarty_function_cycle(array('values' => "bgDark,bgLight"), $this);?>
'>
						<td><?php echo $this->_tpl_vars['nutrient']['nutrientName']; ?>
</td>
						<td style='text-align: center;'><?php echo $this->_tpl_vars['nutrient']['total'];  echo $this->_tpl_vars['nutrient']['units']; ?>
</td>
			<?php if ($this->_tpl_vars['nutrient']['percentDri'] != "--"): ?>
						<td style='text-align: center;'><?php echo $this->_tpl_vars['nutrient']['percentDri']; ?>
&#37;</td>
			<?php else: ?>
						<td style='text-align: center;'>--</td>
			<?php endif; ?>
			<?php $_from = $this->_tpl_vars['nutrient']['quantities']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['quantity']):
?>
						<td style='text-align: center;'><?php echo $this->_tpl_vars['quantity']; ?>
</td>
			<?php endforeach; endif; unset($_from); ?>
					</tr>
		<?php endforeach; endif; unset($_from); ?>
				</table>
			</div>
	<?php endif;  else: ?>
			<div class='standardMargins' style='text-align: justify;'>
				This day has no diary entries.  If you would like to add a food or a meal, search for or view an item as
				you normally would and then use the form at the bottom of the nutrition summary page to add the item
				to a diary.  Using the "timestamp" field you can add a food or meal to this day.  Here's a tip:  create 
				a collection of saved foods that you eat frequently and use them as the building blocks for diaries and meals.
				If you want to add a note to this diary, you may do so with the form below.
			</div>
<?php endif; ?>
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
