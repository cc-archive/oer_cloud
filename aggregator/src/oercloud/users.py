from datetime import datetime

from sqlalchemy import Table, Column, Integer, String, DateTime, ForeignKey
from sqlalchemy.orm import mapper, relation
from oercloud.db import metadata, Session

from feed import Feed
from urls import Bookmark

users_table = Table('sc_users', metadata,
                    Column('uId', Integer, primary_key=True),
                    Column('username', String(128)),
                    Column('password', String(40)),
                    Column('uDatetime', DateTime),
                    Column('uModified', DateTime),
                    Column('name', String(50)),
                    Column('email', String(50)),
                    Column('homepage', String(255)),
                    Column('uContent', String),
                    Column('uIp', String(15)),
                    Column('uStatus', Integer),
                    Column('isFlagged', Integer),
                    Column('isAdmin', Integer),
                    Column('activation_key', String(32)),
                    )

class User(object):

    def __init__(self, username):

        self.username = username
        self.uDatetime = datetime.now()
        self.uModified = datetime.now()
        self.uStatus = 1
        self.isFlagged = 0
        self.isAdmin = 0

    @classmethod
    def by_name_url(self, name, url):
        """Find or create the user with the specified name and homepage."""

        session = Session()

        # first query for the user
        user = session.query(User).filter_by(username=name.strip(),
                                             homepage=url.strip())

        if user.count() > 0:
            # the user exists
            return user.first()

        # the user does not exist; create
        user = User(name)
        user.homepage = url
        user.password = hash(name)
        user.email = 'webmaster@oercloud.creativecommons.org'
        
        session.save(user)
        session.commit()

        return user

mapper(User, users_table, properties = {
        'feeds':relation(Feed, backref='user'),
        'bookmarks':relation(Bookmark, backref='user'),
        })
