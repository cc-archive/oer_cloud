#!/usr/bin/env python

#
# A script for importing OER resource information from a delimited text file
# and then import it into the database which backs http://oercloud.cc.org
#

import md5
import datetime
import logging
import csv  # http://docs.python.org/lib/module-csv.html
import sqlalchemy

# the CSV file to work with
import_file = "/home/nkinkade/cc/cclearn/CoursesLibraries070207.csv"

# setup the database
db = sqlalchemy.create_engine('mysql://root:tahiti3@localhost/oer', convert_unicode=True)
metadata = sqlalchemy.MetaData(db)
bookmarks = sqlalchemy.Table('sc_bookmarks', metadata, autoload=True)
tags_tbl = sqlalchemy.Table('sc_tags', metadata, autoload=True)

# configure the logger.  see: http://docs.python.org/lib/module-logging.html
logging.basicConfig(
	format='%(levelname)-8s %(message)s',
	filename='import_oer_commons.log',
	filemode='w'
)

# Define the structure of the file.  The file actually conforms to most of
# the defaults of the csv module, but I explicity define them here just
# for the sake of clarity, and I also set some rules regarding quoting
# and whitespace
csv.register_dialect(
	'oer',
	delimiter=',',
	lineterminator="\r\n",
	quoting=csv.QUOTE_MINIMAL,
	skipinitialspace=True
)

# parse the file we opened earlier
reader = csv.reader(open(import_file), 'oer')

for row in reader:
	title, address, tags = row
	time = datetime.datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S')
	hash = md5.new(address).hexdigest()

	# the title and address fields in the db is only 255 chars wide.
	# truncate the values if they are larger than 255 and write a log message.
	if len(title) > 255:
		logging.warning('Title for URL %s of length %d truncated to 255 chars.', address, len(title))
		title = title[:255]

	# build a dict representing the new row that we will insert
	row = {
		'uId': '8',
		'bTitle': title,
		'bAddress': address,
		'bDateTime': time,
		'bModified': time,
		'bHast': hash
	}

	try:
		result = bookmarks.insert().execute(**row)
	except sqlalchemy.exceptions.SQLError, e:
		logging.error('Failed to add %s : %s', address, e.args)
		pass

	# if the query inserted a row then we can go ahead and add the tags
	if result.rowcount == 1:
		# grab the id of the bookmark we just inserted
		bId = result.lastrowid

		# work through the list of tags that were split out from the row above
		# the delimiter is a pipe symbol
		tags = tags.split('|')

		for tag in tags:
			# remove possible whitespace
			tag = tag.strip()

			# the tag field in the db is only 32 chars wide.  if this tag is wider
			# truncate the tag name and write a log message.
			if len(tag) > 32:
				logging.warning('Tag for bId %d of length %d truncated to 32 chars.', bId, len(tag))
				tag = tag[:32]

			row = {'bId': bId, 'tag': tag}

			# the import file may possible have the same tag listed twice for a given item,
			# which will raise an exception due to a duplicate key violation in mysql.  just
			# ignore such errors
			try:
				result = tags_tbl.insert().execute(**row)
			except sqlalchemy.exceptions.SQLError, e:
				logging.error('For bId %d : %s', bId, e.args)
				pass
