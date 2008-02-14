import logging

LOG = logging.getLogger("cc.aggregator")
LOG.setLevel(logging.DEBUG)

LOG.addHandler(logging.StreamHandler())
