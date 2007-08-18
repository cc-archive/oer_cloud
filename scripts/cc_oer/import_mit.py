#!/usr/bin/env python

import md5
from datetime import datetime
import logging
from lxml import etree
import warnings
import sqlalchemy

# cause warnings to raise exceptions.  this will allow me to catch and examine
# any SQL warnings, like data truncation, that would otherwise have
# just been issued to stderr
warnings.filterwarnings(action='error', message='.*')

# setup the database
db = sqlalchemy.create_engine('mysql://root:tahiti3@localhost/oer', convert_unicode=True)
metadata = sqlalchemy.MetaData(db)
bookmarks = sqlalchemy.Table('sc_bookmarks', metadata, autoload=True)
tags = sqlalchemy.Table('sc_tags', metadata, autoload=True)

# configure the logger.  see: http://docs.python.org/lib/module-logging.html
logging.basicConfig(
	format='%(levelname)-8s %(message)s',
	filename='import_mit.log',
	filemode='w'
)

# XML namespaces used by this document
namespaces = {
	"rdf": "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
	"dc": "http://purl.org/dc/elements/1.1/",
	"default": "http://purl.org/rss/1.0/"
}

# parse the XML file .. in this case it's an RSS feed
xml_doc = etree.parse("/home/nkinkade/cc/cclearn/mit-allcourses.xml")

for item in xml_doc.xpath("/rdf:RDF/default:item", namespaces):
	title = item.xpath("default:title", namespaces)[0].text
	address = item.xpath("default:link", namespaces)[0].text
	description = item.xpath("default:description", namespaces)[0].text
	time = datetime.utcnow().strftime("%Y-%m-%d %H:%M:%S")
	hash = md5.new(address).hexdigest()

	# the title and address fields in the db is only 255 chars wide.
	# truncate the values if they are larger than 255 and write a log message.
	if len(title) > 255:
		logging.warning('Title for URL %s of length %d truncated to 255 chars.', address, len(title))
		title = title[:255]

	if len(description) > 255:
		logging.warning('Description for URL %s of length %d truncated to 255 chars.', address, len(description))
		description = description[:255]

	print str(len(description))

	# build a dict representing the new row that we will insert
	row = {
		'uId': '16',
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
		logging.error('Failed to add %s : %s', address, e.args)
		pass

	# if the query inserted a row then we can go ahead and add the tags
	if result.rowcount == 1:
		# grab the id of the bookmark we just inserted
		bId = result.lastrowid
		for tag in item.xpath("dc:subject", namespaces):
			
			# the tag field in the db is only 32 chars wide.  if this tag is wider
			# truncate the tag name and write a log message.
			if len(tag.text) > 32:
				logging.warning('Tag for bId %d of length %d truncated to 32 chars.', bId, len(tag.text))
				tag.text = tag.text[:32]

			row = {'bId': bId, 'tag': tag.text}

			# the import file may possible have the same tag listed twice for a given item,
			# which will raise an exception due to a duplicate key violation in mysql.  just
			# ignore such errors
			try:
				result = tags.insert().execute(**row)
			except sqlalchemy.exceptions.SQLError, e:
				logging.error('For bId %d : %s', bId, e.args)
				pass
