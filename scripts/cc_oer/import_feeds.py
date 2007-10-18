#!/usr/bin/env python

# This script is intended to pull a list of OER XML feeds from a specific
# database table, and then incrementally go through those feeds, pull the feed
# down, parse it, and then insert the feed data into the OER Cloud database

import sys
import md5
import time
from datetime import datetime
import feedparser
import warnings
import sqlalchemy

# cause warnings to raise exceptions.  this will allow us to catch and examine
# any SQL warnings, like data truncation, that would otherwise have just been
# issued to stderr
warnings.filterwarnings(action='error', message='.*')

# setup database connectivity
db = sqlalchemy.create_engine('mysql://root@localhost/oercloud', convert_unicode=True)
metadata = sqlalchemy.MetaData(db)
oer_feeds = sqlalchemy.Table('oer_feeds', metadata, autoload=True)
bookmarks = sqlalchemy.Table('sc_bookmarks', metadata, autoload=True)
tags = sqlalchemy.Table('sc_tags', metadata, autoload=True)

# grab all of the feeds from the database
feeds = oer_feeds.select().execute().fetchall()

for feed in feeds:
	print 'Processing feed: %s' % feed.url

	# setup some counters that will simply be used to output some stats
	bookmark_count = 0
	tag_count = 0

	# parse the current feed with feedparser (http://feedparser.org)
	entries = feedparser.parse(feed.url).entries

	for entry in entries:
		# make the date format the the OER Cloud database (scuttle) is expecting
		pretty_datetime = time.strftime('%Y-%m-%d %H:%M:%S', time.gmtime())

		# before doing anything, we check to see if this bookmark already exists
		# in the database, if it does the we skip this entry.  since every url
		# also has an md5 hash stored in the db, then can easily look for that
		# value
		bookmark_hash = md5.new(entry.link).hexdigest()
		result = bookmarks.select(bookmarks.columns.bHash == bookmark_hash).execute()
		if result.rowcount == 1:
			continue

		# build a dict representing the new row that we will insert.  for
		# bTitle, if for any reason there isn't a title for an entry, which
		# would be crazy, but not inconceivable, then just plug the link in the
		# title field.  we assume that a feed will at the very least will not be
		# without a link.
		row = {
			'uId': feed.user_id,
			'bTitle': (entry.link, entry.title)[len(entry.title) > 0],
			'bDescription': entry.summary,
			'bAddress': entry.link,
			'bDatetime': pretty_datetime,
			'bModified': pretty_datetime,
			'bHash': bookmark_hash
		}

		try:
			result = bookmarks.insert().execute(**row)
		except sqlalchemy.exceptions.SQLError, e:
			pass

		# if the query inserted a row then we can go ahead and add the tags, if
		# there are any
		if result.rowcount == 1:
			bookmark_count += 1
			if 'tags' in entry:
				# grab the id of the bookmark we just inserted
				bId = result.lastrowid
				for tag in entry.tags:
					row = {'bId': bId, 'tag': tag.term}

					# the import file may possibly have the same tag listed twice
					# for a given item, which will raise an exception due to a
					# duplicate key violation in mysql.  just ignore such errors
					try:
						result = tags.insert().execute(**row)
					except sqlalchemy.exceptions.SQLError, e:
						pass
					else:
						tag_count += 1

	# update the last_import field for this feed.
	oer_feeds.update(oer_feeds.columns.id == feed.id).execute(last_import = int(time.time()))

	# spit out some simple stats for the user
	print '\tImported %d new bookmarks' % bookmark_count
	print '\tImported %d new tags' % tag_count
