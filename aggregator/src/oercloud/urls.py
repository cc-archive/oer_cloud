import hashlib
from datetime import datetime

from sqlalchemy import Table, Column, Integer, String, DateTime, ForeignKey
from sqlalchemy.orm import mapper, relation
from oercloud.db import metadata, Session

bookmarks_table = Table('sc_bookmarks', metadata,
                        Column('bId', Integer, primary_key=True),
                        Column('uId', Integer, ForeignKey('sc_users.uId')),
                        Column('bIp', String(40)),
                        Column('bStatus', Integer),
                        Column('bDatetime', DateTime),
                        Column('bModified', DateTime),
                        Column('bTitle', String),
                        Column('bAddress', String),
                        Column('bDescription', String),
                        Column('bHash', String(32)),
                        Column('bFlagCount', Integer),
                        Column('bFlaggedBy', String),
                        )

class Bookmark(object):
    
    def __init__(self, url, title, description):

        self.bAddress = url
        self.bTitle = title
        self.bDescription = description

        self.bStatus = 0;
        self.bDatetime = datetime.now()
        self.bModified = datetime.now()

        self.update_hash()

    @classmethod
    def by_url_user(cls, url, user):
        """Get or create the Bookmark with the specified URL and User."""

        session = Session()

        # first query for the bookmark
        bookmark = session.query(Bookmark).filter_by(uId=user.uId,
                                                     bAddress=url)

        if bookmark.first():
            return bookmark.first()

        # the bookmark does not exist; create it
        bookmark = Bookmark(url, '', '')
        bookmark.uId = user.uId
        
        session.save(bookmark)
        session.commit()

        return bookmark

    def update_hash(self):

        self.bHash = hashlib.md5(self.bAddress).hexdigest()

class TagList(list):
    """Custom collection class with custom __contains__ implementation."""

    def __contains__(self, item):

        for t in self:
            if t.tag.lower() == item.tag.lower():
                return True

        return False

tags_table = Table('sc_tags', metadata,
                   Column('id', Integer, primary_key=True),
                   Column('bId', Integer, ForeignKey('sc_bookmarks.bId')),
                   Column('tag', String(255)),
                   )

class Tag(object):

    def __init__(self, tag):
        self.tag = tag

mapper(Bookmark, bookmarks_table, properties = {
        'tags':relation(Tag, backref='bookmark', collection_class=TagList),
        })
mapper(Tag, tags_table)
