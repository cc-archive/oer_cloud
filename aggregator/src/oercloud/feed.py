from sqlalchemy import Table, Column, Integer, String, ForeignKey
from sqlalchemy.orm import mapper
from oercloud import metadata

# feed type "contstants"
OPML = 'opml'
RSS10 = 'rss10'
RSS20 = 'rss20'
OAIPMH= 'oai-pmh'

feed_table = Table('oer_feeds', metadata,
                   Column('id', Integer, primary_key=True),
                   Column('url', String(255)),
                   Column('user_id', Integer, ForeignKey('sc_users.uId')),
                   Column('last_import', Integer),
                   Column('feed_type', String(16)),
                   )

class Feed(object):

    # XXX one week update interval...
    update_interval = 604800

    def __init__(self, url, user_id, last_import, feed_type):

        self.url = url
        self.user_id = user_id
        self.last_import = last_import
        self.feed_type = feed_type

    def __str__(self):
        return "%s (%s)" % (self.url, self.feed_type)

mapper(Feed, feed_table)
