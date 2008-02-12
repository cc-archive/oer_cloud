CC Learn Scripts
================

This package contains support scripts for CC Learn.  At this point it consists
only of import scripts for open education search resources.

Getting Started
---------------

This package uses zc.buildout to manage scripts and dependencies.  To begin
working with the scripts, do the following::

  $ python bootstrap.py
  $ ./bin/buildout

Runing buildout will process setup.py and download any dependencies needed.
It will also create an executable script in bin for each console_scripts
entrty point defined in setup.py.

Additional Resources
--------------------

* http://python.org/pypi/zc.buildout
* setuptools (use the Google, Luke)
