"""Command line interface and primary entry point for the CC Aggregator."""
import time

import pkg_resources
import opml

import aggregator
import aggregator.handlers
import oercloud

def update_feed_list(opml):
    """Load an OPML source and add any feeds that do not exist to our
    database."""

    session = oercloud.Session()

    # see if this needs handled
    for item in opml:

        # see if this is an inclusion
        if item.type == 'link':

            # see if it's an OPML inclusion
            if item.url[-5:] == '.opml':
                # its OPML -- follow the link
                aggregator.LOG.debug("Following OPML inclusion to %s" %
                                     item.url)
                update_feed_list(opml.parse(item.url))

        else:
            # not an inclusion -- add it to our feed list if needed
            if session.query(oercloud.Feed).filter_by(
                url = item.xmlUrl).count() == 0:

                # new feed -- find the appropriate user
                user = oercloud.User.by_name_url(
                    item.text, item.xmlUrl)

                aggregator.LOG.info("Adding feed: %s" % item.xmlUrl)

                session.save(
                    oercloud.Feed(item.xmlUrl, user.uId, 0, item.type)
                    )

        session.commit()

        # finally, recurse to check for sub-elements
        update_feed_list(item)

def check_feeds():
    """Check each feed and see if it needs to be updated."""

    session = oercloud.Session()

    # load the entry point handlers for different feed types
    handlers = aggregator.handlers.get()

    for feed in session.query(oercloud.Feed):

        if (time.time() - feed.last_import) > feed.update_interval:

            # this feed needs updated -- call the appropriate handler
            aggregator.LOG.info("Updating %s" % feed)

            if feed.feed_type in handlers:
                handlers[feed.feed_type].load()(feed)
            else:
                # no handler... log a warning
                pass

def update():
    """Perform a full update, end to end."""

    # load the OPML file and update any feeds
    for o in oercloud.Session().query(oercloud.Feed).filter_by(
        feed_type=oercloud.feed.OPML):
        
        aggregator.LOG.info("Loading OPML from %s" % o.url)
        update_feed_list(opml.parse(o.url))

    # check each feed and see if it should be polled
    check_feeds()


def cli():
    """Command line interface to the aggregator."""

    # XXX load the option parser and parser the command line

    aggregator.LOG.debug("Beginning feed update process.")
    update()
