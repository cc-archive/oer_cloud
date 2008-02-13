from sqlalchemy import MetaData, create_engine
from sqlalchemy.orm import sessionmaker

# setup database connectivity
db = create_engine('mysql://root@localhost/oercloud', convert_unicode=True)

metadata = MetaData(db)

# load our mapppings
from feed import Feed

# create the sessionmaker
Session = sessionmaker(autoflush=True, transactional=True)
Session.configure(bind=db)

     
