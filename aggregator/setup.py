## Copyright (c) 2007-2008 Nathan R. Yergler, Creative Commons

## Permission is hereby granted, free of charge, to any person obtaining
## a copy of this software and associated documentation files (the "Software"),
## to deal in the Software without restriction, including without limitation
## the rights to use, copy, modify, merge, publish, distribute, sublicense,
## and/or sell copies of the Software, and to permit persons to whom the
## Software is furnished to do so, subject to the following conditions:

## The above copyright notice and this permission notice shall be included in
## all copies or substantial portions of the Software.

## THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
## IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
## FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
## AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
## LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
## FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
## DEALINGS IN THE SOFTWARE.

from setuptools import setup, find_packages

setup(
    name = "aggregator",
    version = "0.2",
    packages = ['aggregator', 'oercloud'],
    package_dir = {'' : 'src'},

    # scripts and dependencies
    install_requires = ['setuptools',
                        'lxml',
                        'opml',
                        'MySQL-python',
                        'SQLAlchemy',
                        ],

    entry_points = { 'console_scripts':
                     ['update = aggregator.cli:cli',
                      ],

                     'cc.aggregator':
                         ['rss10 = aggregator.source.feed:update',
                          'rss20 = aggregator.source.feed:update',
                          'atom  = aggregator.source.feed:update',
                          'oaipmh = aggregator.source.oaipmh:update',
                          ],
                     },

    # author metadata
    author = 'Nathan R. Yergler',
    author_email = 'nathan@creativecommons.org',

    )
