"""Command line interface and primary entry point for the CC Aggregator."""

import pkg_resources
import opml

import aggregator.handlers
import oercloud

def update_feed_list(opml):
    """Load an OPML source and add any feeds that do not exist to our
    database."""

    # see if this needs handled
    for item in outline:

        # see if this is an inclusion
        if item.type == 'link':

            # see if it's an OPML inclusion
            if item.url[-5:] == '.opml':
                # its OPML -- follow the link
                update_feed_list(opml.parse(item.url))

        else:
            # not an inclusion -- add it to our feed list if needed
            if oercloud.Session().query(oercloud.Feed).filter_by(
                url = item.xmlUrl).count() == 0:

                # new feed -- find the appropriate user
                pass

        # finally, recurse to check for sub-elements
        update_feed_list(item)

def check_feeds():
    """Check each feed and see if it needs to be updated."""

    # load the entry point handlers for different feed types
    handlers = aggregator.handlers.get()

    for feed in oercloud.Session().query(oercloud.Feed):

        # XXX
        if True: # (now - feed.last_import) > feed.update_interval:

            # this feed needs updated -- call the appropriate handler
            for item in handlers[feed.feed_type].load()(feed):
                
                bookmark = oercloud.Url.get_or_create(feed.user, item.url)
                bookmark.update_metadata(item)

                bookmark.commit()

def update():
    """Perform a full update, end to end."""

    # load the OPML file and update any feeds
    for o in oercloud.Session().query(oercloud.Feed).filter_by(
        feed_type=oercloud.feed.OPML):
        
        update_feed_list(opml.parse(o.feed_url))

    # check each feed and see if it should be polled
    check_feeds()


def cli():
    """Command line interface to the aggregator."""

    # XXX load the option parser and parser the command line

    update()
