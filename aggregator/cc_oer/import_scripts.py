import sys
import os
import md5
import datetime

import lxml.etree as et

def nsta():
    """Generate output conforming to the del.icio.us export schema suitable
    for import into the CC oercloud system."""

    NSMAP = dict(oai = 'http://www.openarchives.org/OAI/2.0/',
                 oai_dc = 'http://www.openarchives.org/OAI/2.0/oai_dc/',
                 dc = 'http://purl.org/dc/elements/1.1/')

    in_file = et.parse(sys.argv[-1])
    result = et.Element("posts", {'user':'nsta'})

    for record in in_file.xpath('//oai:record', NSMAP):

        title = record.xpath('./oai:metadata/oai_dc:dc/dc:title', NSMAP)[0].text
        desc = record.xpath('./oai:metadata/oai_dc:dc/dc:description', NSMAP)[0].text
        url = record.xpath('./oai:metadata/oai_dc:dc/dc:identifier', NSMAP)[0].text
        tags = [n.text for n in record.xpath('./oai:metadata/oai_dc:dc/dc:subject', NSMAP)]

        et.SubElement(result, 'post', dict(
                href = url,
                description = title,
                extended = desc,
                tag = " ".join([n.replace(' ','_') for n in tags]),
                time = datetime.datetime.utcnow().strftime("%Y-%m-%dT%H:%M:%SZ"),
                hash = md5.new(url).hexdigest()
                )
                      )

    print et.tostring(result)



def oerc():
    """Generate output conforming to the del.icio.us export schema suitable
    for import into the CC oercloud system.  Expected input is the OER Commons
    CSV format."""

    import csv
    
    NSMAP = dict(oai = 'http://www.openarchives.org/OAI/2.0/',
                 oai_dc = 'http://www.openarchives.org/OAI/2.0/oai_dc/',
                 dc = 'http://purl.org/dc/elements/1.1/')

    for in_file in sys.argv[1:]:
        lines = [n for n in csv.reader(file(in_file))]
        result = et.Element("posts", {'user':'oercommons'})

        for record in lines:
            title = unicode(record[0].decode('utf8'))
            desc = ''

            url = record[1]
            tags = [unicode(n) for n in record[2].decode('utf8').split('|')]

            et.SubElement(result, 'post', dict(
                    href = url,
                    description = title,
                    extended = desc,
                    tag = " ".join([n.replace(' ','_') for n in tags]),
                    time = datetime.datetime.utcnow().strftime("%Y-%m-%dT%H:%M:%SZ"),
                    hash = md5.new(url).hexdigest()
                    )
                          )

        file ("%s.xml" % in_file, 'w').write(et.tostring(result))
        print in_file

