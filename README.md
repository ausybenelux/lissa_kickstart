# LISSA Kickstart install profile

The LISSA Kickstart profile is a reusable Drupal 8 based package to manage and
publish content for a LISSA backend.

You can use it by placing the project inside the profiles directory of a clean
Drupal 8 download and running drush make.

It can be automatically setup inside a Vagrant box using the LISSA repository
available at git@gitlab.crosscheck.be:lissa/infrastructure.

## Installation

The LISSA kickstart profile can be included in a drupal 8 core installation by
moving it to the /profiles directory.

The recommended way of setting up the distribution is building the project with
Phing:

- Install the latest version of Phing
- cd to the root of this repository
- Execute the following command: phing -Ddocroot=/path/to/docroot
- Replace /path/to/docroot/parent to the path to the directory where the
  distribution will be installed.

While executing Phing the following steps will be executed:

- Create a docroot directory under /path/to/docroot/parent
- Execute drush make on the build.make file
  - Drush make will set up drupal 8 core
  - Drush make will add the lissa_kickstart profile to the profiles directory
  - Drush make will execute the lissa_kickstart.make file
- Execute drush site-install with the parameters provided in
  build.defaults.properties

### Customizing your installation

You can customize the installation by creating your own build.properties file.
All possible options are documented in build.default.properties.

### Managing your installation

You can use Phing to execute other tasks for this distribution. See the
build.xml file for a list of possible tasks (called targets in Phing).

## Information architecture

### Events

## API

The LISSA backend publishes content through a REST API.

### List published events

GET http://admin.lissa.dev/api/events; Accept: application/ext+json

Example output can be found in lissa_kickstart/modules/lissa/ext/example.json

