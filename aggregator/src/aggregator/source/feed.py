"""feedparser based backend."""

import feedparser

import aggregator
import oercloud

def update(feed):
    """Handle a feedparser-compatible feed;

    Load the feed and return an iterator of Record objects. ?
    """

    aggregator.LOG.debug("Processing %s with feedparser." % feed.url)
    f = feedparser.parse(feed.url)


    for item in f.entries:
        # process each entry in the feed

        # get the bookmark (as entered by this user)
        bookmark = oercloud.Bookmark.by_url_user(item.link, feed.user)
        
        # get the session the bookmark is attached to 
        session = oercloud.Session.object_session(bookmark)

        bookmark.bTitle = item.title
        bookmark.bDescription = item.description

        # add the tags
        if item.has_key('tags'):
            for tag in item.tags:

                # create a Tag object
                bTag = oercloud.Tag(tag['term'])

                # see if it already exists
                if bTag not in bookmark.tags:
                    bookmark.tags.append(bTag)

                
        bookmark.update_hash()
        session.save_or_update(bookmark)

        session.commit()
