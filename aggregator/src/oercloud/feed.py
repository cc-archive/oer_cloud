from sqlalchemy import Table, Column, Integer, String, ForeignKey
from sqlalchemy.orm import mapper
from oercloud import metadata

feed_table = Table('oer_feeds', metadata,
                   Column('id', Integer, primary_key=True),
                   Column('url', String(255)),
                   Column('user_id', Integer),
                   Column('last_import', Integer),
                   Column('feed_type', String(16)),
                   )

class Feed(object):

    def __init__(self, url, user_id, last_import, feed_type):

        self.url = url
        self.user_id = user_id
        self.last_import = last_import
        self.feed_type = feed_type


mapper(Feed, feed_table)
