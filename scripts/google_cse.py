#!/usr/bin/env python

"""Tools for creating XML files for the Google OER Cloud CSE"""

import sqlalchemy
import xml.dom.minidom

# User defined configurations:
db = sqlalchemy.create_engine("mysql://root@localhost/oercloud")
context_fname = "/home/nkinkade/cc/devel/oercloud/api/cse/context.xml"
# This is the base file name for annotations files.  If there are more than one
# then this file path will be appended with '<file_count>.xml', otherwise
# simply '.xml'.
annot_basename = "/home/nkinkade/cc/devel/oercloud/api/cse/annotations"

# Setup the database objects
metadata = sqlalchemy.MetaData(db)
bookmarks_tbl = sqlalchemy.Table("sc_bookmarks", metadata, autoload=True)
users_tbl = sqlalchemy.Table("sc_users", metadata, autoload=True)
tags_tbl = sqlalchemy.Table("sc_tags", metadata, autoload=True)

def get_user(userid):
	"""Takes a user id (uId) and returns the user name."""
	user = users_tbl.select(users_tbl.c.uId == userid).execute().fetchone()
	return user["username"]


def get_tags(bookmarkid):
	"""Returns all tags associated with a given bookmark."""
	return tags_tbl.select(tags_tbl.c.bId == bookmarkid).execute().fetchall()


def create_el(el_name):
	"""Returns an XML element with the given name."""
	return xmldoc.createElement(el_name)


def append_el(child, parent):
	"""Append an XML element to another element."""
	return parent.appendChild(child)


def write_annot(doc, fnum=NULL):
	"""Write out an annotations file."""
	fname = "%s%s.xml" % (annot_basename, fnum)
	fp = open(fname, "w")
	doc.writexml(fp, "", "\t", "\n", "UTF-8")
	fp.close()
	return fnum = fnum + 1


def make_annotations():
	"""Creates the annotations for Google's OER Cloud CSE.
	
	This function may write multiple files depending on how many annotations
	exist.  The maximum file size that Google will accept is 3MB, so if a given
	annotations document approaches 3MB it will be written out to a file and a
	new document will be started.

	"""
	xmldoc = xml.dom.minidom.Document()

	root_el = xmldoc.createElement("GoogleCustomizations")
	append_el(root_el, xmldoc)

	annots_el = create_el("Annotations")
	append_el(annots_el, root_el)

	# Grab all of the booksmarks from the database
	bookmarks = bookmarks_tbl.select().execute().fetchall()

	# This variable will track how many annotation files we have written
	file_count = 0

	for bookmark in bookmarks:
		annot_el = create_el("Annotation")
		annot_el.setAttribute("about", bookmark.bAddress)
		annot_el.setAttribute("score", "1")
		append_el(annot_el, annots_el)

		cse_lbl = create_el("Label")
		cse_lbl.setAttribute("name", "_cse_cclearn_oe_search")
		append_el(cse_lbl, annot_el)

		user_lbl = create_el("Label")
		user_lbl.setAttribute("name", get_user(bookmark.uId))
		append_el(user_lbl, annot_el)

		# Uncomment these lines to include tags/facets
		#tags = get_tags(bookmark.bId)
		#for tag in tags:
		#	tag_lbl = xmldoc.createElement("Label")
		#	tag_lbl.setAttribute("name", tag.tag)
		#	annotation.appendChild(tag_lbl)
		#	tcount = tcount + 1

		if len(xmldoc.toprettyxml()) > 3000000:
			file_count = write_annot(xmldoc, file_count)

	write_annot(xmldoc, file_count)
	return file_count


def make_context(fcount=1):
	"""Creates the principal context file for the Gooogle OER Cloud CSE.

	Depending on the value of the single argument fcount, the file will tell
	Google to include various other annotation files that are created from this
	same module via the make_annotations() function.

	"""
	xmldoc = xml.dom.minidom.Document()

	root_el = create_el("GoogleCustomizations")
	root_el.setAttribute("version", "1.0")
	append_el(root_el, xmldoc)

	cse_el = create_el("CustomSearchEngine")
	cse_el.setAttribute("keywords", "oai")
	cse_el.setAttribute("title", "Open Education Search")
	cse_el.setAttribute("language", "en")
	append_el(cse_el, root_el)

	context_el = create_el("Context")
	append_el(context_el, cse_el)

	bglabels_el = create_el("BackgroundLabels")
	append_el(bglabels_el, context_el)

	cse_lbl = create_el("Label")
	cse_lbl.setAttribute("name", "_cse_cclearn_oe_search")
	cse_lbl.setAttribute("mode", "FILTER")
	append_el(cse_lbl, bglabels_el)

	look_el = create_el("LookAndFeel")
	look_el.setAttribute("nonprofit", "true")
	append_el(look_el, cse_el)

	facet_el = create_el("Facet")
	append_el(facet_el, context_el)

	# we are not outputting facets at the moment due to a problem with the CSE.
	# if at some point in the future we want facets then uncomment the following
	## output the username"s as facets
	#users = users_tbl.select(order_by=users_tbl.c.username).execute().fetchall()
	#for user in users:
	#	fitem_el = create_el("FacetItem")
	#	fitem_el.setAttribute("title", user.username)
	#	append_el(fitem_el, facet_el)
	#
	#	user_lbl = create_el("Label")
	#	user_lbl.setAttribute("name", user.username)
	#	append_el(user_lbl, fitem_el)
	#
	##output each tag as a facet
	#tags = tags_tbl.select(order_by=tags_tbl.c.tag).execute().fetchall()
	#for tag in tags:
	#	fitem_el = create_el("FacetItem")
	#	fitem_el.setAttribute("title", tag.tag)
	#	append_el(fitem_el, facet_el)
	#
	#	# don"t create a facet if the tag was generated by Scuttle i.e. the
	#	# first 7 characters are "system:"
	#	if tag.tag[:7] != "system:":
	#		tag_lbl = create_el("Label")
	#		tag_lbl.setAttribute("name", tag.tag)
	#		tag_lbl.setAttribute("mode", "FILTER")
	#		append_el(tag_lbl, fitem_el)

	# Write out as many includes as there are annotation files as determined by
	# the argument fcount.
	for idx in range(fcount):
		include_el = create_el("Include")
		include_el.setAttribute("type", "Annotations")
		include_el.setAttribute("href", 
			"http://oercloud.creativecommons.org/api/google_cse/annotations%d.xml"
			% idx)
		append_el(include_el, root_el)

	fp = open(context_fname, "w")
	doc.writexml(fp, "", "\t", "\n", "UTF-8")
	fp.close()

if __name__ == "__main__":
	file_count = make_annotations()
	make_context(file_count)
