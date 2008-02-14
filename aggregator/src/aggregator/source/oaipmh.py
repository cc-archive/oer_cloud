"""OAI-PMH backend."""

import aggregator

def update(source):

    aggregator.LOG.debug("Processing %s as OAI-PMH" % source.url)
