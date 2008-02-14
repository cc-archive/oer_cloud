from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker

# setup database connectivity
db = sqlalchemy.create_engine('mysql://root@localhost/oercloud', 
                              convert_unicode=True)

metadata = sqlalchemy.MetaData(db)
Feed = sqlalchemy.Table('oer_feeds', metadata, autoload=True)
Bookmark = sqlalchemy.Table('sc_bookmarks', metadata, autoload=True)

Session = sessionmaker(autoflush=True, transactional=True)
Session.configure(bind=db)


