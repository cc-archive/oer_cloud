"""Command line interface and primary entry point for the CC Aggregator."""

import pkg_resources

import aggregator.handlers
import oercloud

def update_feed_list(opml_source):
    """Load an OPML source and add any feeds that do not exist to our
    database."""
    
    # XXX

def check_feeds():
    """Check each feed and see if it needs to be updated."""

    # load the entry point handlers for different feed types
    handlers = aggregator.handlers.get()

    for feed in oercloud.Session().query(oercloud.Feed):

        # XXX
        if True: # (now - feed.last_import) > feed.update_interval:

            # this feed needs updated -- call the appropriate handler
            for item in handlers[feed.feed_type].load()(feed):
                
                bookmark = oercloud.Bookmark.get_or_create(feed.user, item.url)
                bookmark.update_metadata(item)

                bookmark.commit()

def update():
    """Perform a full update, end to end."""

    # load the OPML file and update any feeds
    # XXX
    #for o in opml_urls:
    #    update_feed_list(o)

    # check each feed and see if it should be polled
    check_feeds()


def cli():
    """Command line interface to the aggregator."""

    # XXX load the option parser and parser the command line

    update()
