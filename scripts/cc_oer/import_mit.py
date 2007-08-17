#!/usr/bin/env python

import md5
import datetime
import MySQLdb as db
import logging
from lxml import etree

# configure the logger.  see: http://docs.python.org/lib/module-logging.html
logging.basicConfig(
	format='%(levelname)-8s %(message)s',
	filename='import_xml.log',
	filemode='w'
)

# mysql connection params
mysql = {
	"host": "127.0.0.1",
	"user": "root",
	"passwd": "tahiti3",
	"db": "oer",
	"charset": "utf8"
}

# XML namespaces used by this document
namespaces = {
	"rdf": "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
	"dc": "http://purl.org/dc/elements/1.1/",
	"default": "http://purl.org/rss/1.0/"
}

# connect to the db and get a cursor to work with
db_conn = db.Connect(**mysql)
db_cursor = db_conn.cursor()

# parse the XML file .. in this case it's an RSS feed
xml_doc = etree.parse("/home/nkinkade/cc/cclearn/mit-allcourses.xml")

for item in xml_doc.xpath("/rdf:RDF/default:item", namespaces):
	title = item.xpath("default:title", namespaces)[0].text
	address = item.xpath("default:link", namespaces)[0].text
	description = item.xpath("default:description", namespaces)[0].text
	time = datetime.datetime.utcnow().strftime("%Y-%m-%d %H:%M:%S")
	hash = md5.new(address).hexdigest()

	# the title and address fields in the db is only 255 chars wide.
	# truncate the values if they are larger than 255 and write a log message.
	if len(title) > 255:
		logging.warning("Title for URL %s of length %d truncated to 255 chars.", address, len(title))
		title = title[:255]

	if len(description) > 255:
		logging.warning("Description for URL %s of length %d truncated to 255 chars.", address, len(description))
		description = description[:255]

	sql = """
		INSERT INTO sc_bookmarks (
			bTitle, bAddress, bDescription, bDateTime, bModified, bHash, uId
		)
		VALUES (%s, %s, %s, %s, %s, %s, %s)
	"""

	# the execute() method returns how many rows were affected or selected.
	# so if our insert statement was successful then add the tags too
	if db_cursor.execute(sql, (title, address, description, time, time, hash, "16")) == 1:
		# grab the id of the bookmark we just inserted
		bId = db_cursor.lastrowid
		for tag in item.xpath("dc:subject", namespaces):
			sql = """
				INSERT INTO sc_tags (bId, tag)
				VALUES (%s, %s)
			"""
			
			# the tag field in the db is only 32 chars wide.  if this tag is wider
			# truncate the tag name and write a log message.
			if len(tag.text) > 32:
				logging.warning("Tag for bId %d of length %d truncated to 32 chars.", bId, len(tag.text))
				tag.text = tag.text[:32]

			# the import file may possible have the same tag listed twice for a given item,
			# which will raise an exception due to a duplicate key violation in mysql.  just
			# ignore such errors
			try:
				db_cursor.execute(sql, (bId, tag.text))
			except db.IntegrityError:
				logging.error("Duplicate tag %s not inserted for bId %d.", tag.text, bId)
				pass
