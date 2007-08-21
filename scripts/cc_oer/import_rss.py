#!/usr/bin/env python

import sys
import md5
from datetime import datetime
import logging
from lxml import etree
import warnings
import sqlalchemy

##### -------------- SET THIS --------------- #####
user_id = '16'  # user who will own these bookmarks
##### --------------------------------------- #####

# the RSS file to import
import_file = sys.argv[-1]

# cause warnings to raise exceptions.  this will allow us to catch and examine
# any SQL warnings, like data truncation, that would otherwise have
# just been issued to stderr
warnings.filterwarnings(action='error', message='.*')

# setup database connectivity
db = sqlalchemy.create_engine('mysql://root:ccadmin@localhost/oer', convert_unicode=True)
metadata = sqlalchemy.MetaData(db)
bookmarks = sqlalchemy.Table('sc_bookmarks', metadata, autoload=True)
tags = sqlalchemy.Table('sc_tags', metadata, autoload=True)

# configure the logger.  see: http://docs.python.org/lib/module-logging.html
logging.basicConfig(
	format='%(levelname)-8s %(message)s',
	filename='import_rss.log',
	filemode='w'
)

# XML namespaces used by this document
namespaces = {
	"rdf": "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
	"dc": "http://purl.org/dc/elements/1.1/",
	"default": "http://purl.org/rss/1.0/"
}

# initialize some counters so that we can report stats to the user
# when the script is done
bookmark_count = 0
tag_count = 0
sql_errors = 0

# parse the XML file .. in this case it's an RSS feed
xml_doc = etree.parse(import_file)

for item in xml_doc.xpath("/rdf:RDF/default:item", namespaces):
	result = '' # set result to an empty string just in case
	title = item.xpath("default:title", namespaces)[0].text
	address = item.xpath("default:link", namespaces)[0].text
	description = item.xpath("default:description", namespaces)[0].text
	time = datetime.utcnow().strftime("%Y-%m-%d %H:%M:%S")
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
		logging.error('SQL error while adding bookmark %s : %s', address, e.args)
		sql_errors += 1

	# if the query inserted a row then we can go ahead and add the tags
	if result.rowcount == 1:
		print '.',
		bookmark_count += 1
		# grab the id of the bookmark we just inserted
		bId = result.lastrowid
		for tag in item.xpath("dc:subject", namespaces):
			
			row = {'bId': bId, 'tag': tag.text}

			# the import file may possible have the same tag listed twice for a given item,
			# which will raise an exception due to a duplicate key violation in mysql.  just
			# ignore such errors
			try:
				result = tags.insert().execute(**row)
			except sqlalchemy.exceptions.SQLError, e:
				logging.error('SQL error for bId %d : %s', bId, e.args)
				sql_errors += 1
			else:
				if result.rowcount == 1:
					tag_count += 1


print '\n'
print 'Bookmarks added: %d' % bookmark_count
print 'Tags added: %d' % tag_count
print 'SQL errors/warnings issued: %d' % sql_errors
print '\nSee import_csv.log for SQL warning and error details.'
