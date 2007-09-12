#!/usr/bin/env python

#
# A script for importing OER resource information from a delimited text file
# and then import it into the database which backs http://oercloud.cc.org
#

import sys
import md5
from datetime import datetime
import logging
import warnings
import csv  # http://docs.python.org/lib/module-csv.html
import sqlalchemy

# WARNING: this script expects that there will be 4 columns and in this exact order:
# 	Title, URL, Description, tags
# You may define the properties of the delimited file in terms of delimiters and
# such via the "delimiter" and "lineterminator" parameters below.  DONT FORGET
# to remove the column title row, otherwise this script will import it.

##### --------------- SET THESE VALUES CORRECTLY ------------------- #####
field_delimiter = '^'
line_terminator = '\n'
tag_delimiter = ','  # specify the delimiter for the items in tag column
user_id = '19'  # the id of the user to who the bookmarks will be assigned
##### -------------------------------------------------------------- #####

# parse the file passed in as the last argument
reader = csv.reader(open(sys.argv[-1]), delimiter=field_delimiter, lineterminator=line_terminator)

# setup database connectivity
db = sqlalchemy.create_engine('mysql://root:ccadmin@localhost/oer', convert_unicode=True)
metadata = sqlalchemy.MetaData(db)
bookmarks = sqlalchemy.Table('sc_bookmarks', metadata, autoload=True)
tags_tbl = sqlalchemy.Table('sc_tags', metadata, autoload=True)

# configure the logger.  see: http://docs.python.org/lib/module-logging.html
logging.basicConfig(
	format='%(levelname)-8s %(message)s',
	filename='import_csv.log',
	filemode='w'
)

# cause warnings to raise exceptions.  this will allow us to catch and examine
# any SQL warnings, like data truncation, that would otherwise have
# just been issued to stderr
warnings.filterwarnings(action='error', message='.*')

# initialize some counters so that we can report stats to the user
# when the script is done
bookmark_count = 0
tag_count = 0
sql_errors = 0

for title, address, description, tags in reader:
	result = '' # just in case, set result to an empty string
	time = datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S')
	hash = md5.new(address).hexdigest()

	# build a dict representing the new row that we will insert
	row = {
		'uId': user_id,
		'bTitle': title,
		'bAddress': address,
		'bDescription': description,
		'bDatetime': time,
		'bModified': time,
		'bHash': hash
	}

	try:
		result = bookmarks.insert().execute(**row)
	except sqlalchemy.exceptions.SQLError, e:
		logging.error('SQL error for address %s : %s', address, e.args)
		sql_errors += 1

	# if the query inserted a row then we can go ahead and add the tags
	if result.rowcount == 1:
		print '.',
		bookmark_count += 1

		# grab the id of the bookmark we just inserted
		bId = result.lastrowid

		if len(tags.strip()) != 0:
			# work through the list of tags that were split out from the row above
			# the delimiter is a pipe symbol
			tags = tags.split(tag_delimiter)

			for tag in tags:

				# remove possible whitespace
				tag = tag.strip()

				row = {'bId': bId, 'tag': tag}

				# the import file may possible have the same tag listed twice for a given item,
				# which will raise an exception due to a duplicate key violation in mysql.  just
				# ignore such errors
				try:
					result = tags_tbl.insert().execute(**row)
				except sqlalchemy.exceptions.SQLError, e:
					logging.error('SQL error for bId %d : %s', bId, e.args)
					sql_errors += 1
				else:
					if result.rowcount == 1:
						tag_count += 1

print '\n'
print 'Bookmarks added: %d' % bookmark_count
print 'Tags added: %d' % tag_count
print 'SQL errors issued: %d' % sql_errors
print '\nSee import_csv.log for SQL warning and error details.'
