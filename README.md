# J@m; Configuration

This package provides a configuration interfaces that hides storage of configuration values from its access in a program.


## Installation

To install Jm_Console you can use the PEAR installer or get a tarball and install the files manually.

___
### Using the PEAR installer

If you haven't discovered the metashock pear channel yet you'll have to do it. Also you should issue a channel update:

    pear channel-discover metashock.de/pear
    pear channel-update metashock

After this you can install Jm_Console. The following command will install the lastest stable version with its dependencies:

    pear install -a metashock/Jm_Configuration

If you want to install a specific version or a beta version you'll have to specify this version on the command line. For example:

    pear install -a metashock/Jm_Configuration-0.1.0

___
### Manually download and install files

Alternatively, you can just download the package from http://www.metashock.de/pear and put into a folder listed in your include_path. Please refer to the php.net documentation of the include_path directive.


