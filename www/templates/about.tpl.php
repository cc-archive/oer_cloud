<?php
$this->includeTemplate($GLOBALS['top_include']);
?>


<h3 id="oer">Open Educational Resource links</h3>
<p>Help us collect and tag <a href="http://en.wikipedia.org/wiki/Open_educational_resources">Open Education Resource</a> links to seed a web scale OER search engine.  See <a href="http://learn.creativecommons.org/oer-search-faq/">this FAQ</a> for details.</p>

<p>While this collection of links and tags is in the public domain, the OERs linked to are available under CC licenses or other terms specified by their owners; check each site for details.</p>

<p>This site is run by <a href="http://learn.creativecommons.org">ccLearn</a>, a project of <a href="http://creativecommons.org">Creative Commons</a>, with support from <a href="http://www.hewlett.org">The William and Flora Hewlett Foundation</a>.</p>

<h3 id="geek"><?php echo T_('Geek Stuff'); ?></h3>
<ul>
<li><?php echo sprintf(T_('%s is based on <a href="http://sourceforge.net/projects/scuttle/">an open-source project</a> licensed under the <a href="http://www.gnu.org/copyleft/gpl.html"><acronym title="GNU\'s Not Unix">GNU</acronym> General Public License</a>. This means you can host it on your own web server for free, whether it is on the Internet, a private network or just your own computer.'), $GLOBALS['sitename']); ?></li>
<li>ccLearn modifications are available from our <a href="http://wiki.creativecommons.org/Source_Repository_Information">source repository</a>.</li>
<li><?php echo sprintf(T_('%1$s supports most of the <a href="http://del.icio.us/doc/api">del.icio.us <abbr title="Application Programming Interface">API</abbr></a>. Almost all of the neat tools made for that system can be modified to work with %1$s instead. If you find a tool that won\'t let you change the API address, ask the creator to add this setting. You never know, they might just do it.'), $GLOBALS['sitename']); ?></li>
</ul>

<?php
$this->includeTemplate($GLOBALS['bottom_include']);
?>
