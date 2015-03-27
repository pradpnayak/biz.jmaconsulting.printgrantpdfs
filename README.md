biz.jmaconsulting.printgrantpdfs
================================

Installation
------------

1. As part of your general CiviCRM installation, you should set up a cron job following the instructions at http://wiki.civicrm.org/confluence/display/CRMDOC/Managing+Scheduled+Jobs#ManagingScheduledJobs-Command-lineSyntaxforRunningJobs
2. As part of your general CiviCRM installation, you should set a CiviCRM Extensions Directory at Administer >> System Settings >> Directories.
3. As part of your general CiviCRM installation, you should set an Extension Resource URL at Administer >> System Settings >> Resource URLs.
4. Navigate to Administer >> System Settings >> Manage Extensions.
5. Beside Print Grant PDFs click Install.
6. If you want to be able to include .doc or .docx Word attachments in the printouts, you need to install unoconv. This requires Linux sysadmin skills beyond the novice level. There are lots of google results of people having problems with this install, and various workarounds depending on you flavour and version of Linux and what is currently installed on it. We've had trouble on some of our servers. Unfortunately, we don't offer free support for this installation.
6.1. On a command line:
`# sudo apt-get update
`# sudo apt-get install unoconv
This should install the following dependencies, which amount to about half of LibreOffice:
unoconv
  Depends: python
  Depends: python-uno
python-uno
  Depends: libreoffice-core
  Depends: python2.7
  Depends: python
  Depends: libc6
  Depends: libgcc1
  Depends: libpython2.7
  Depends: libstdc++6
  Depends: uno-libs3
  Depends: ure

Use
---

1. Navigate to Grants > Find Grants, select criteria for search, click Search.
2. Select all or some of the results.
3. On the Actions dropdown, select Print Grants as PDFs.
4. Save the resulting download file to a suitable place on your local hard disk.
5. Unzip the file to access the individual grant PDF printouts.
