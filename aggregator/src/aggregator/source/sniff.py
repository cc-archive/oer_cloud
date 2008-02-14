import oaipmh.client as oaiclient

import oercloud.feed

import aggregator
import aggregator.handlers

def update(feed):
    """Sniff the actual type of the feed and dispatch to that handler."""

    aggregator.LOG.debug("Sniffing feed type for %s" % feed.url)

    # ask the feed to Identify itself as OAI-PMH
    oai_client = oaiclient.Client(feed.url)

    try:
        oai_client.identify()

        # no error raised, assume it's OAI-PMH
        handlers[oercloud.feed.OAIPMH].load()(feed)

    except oaipmh.error.XMLSyntaxError, e:

        # not OAI-PMH
        handlers[oercloud.feed.RSS20].load()(feed)
