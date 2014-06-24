# LISSA Kickstart install profile

The LISSA Kickstart profile is a reusable Drupal 8 based package to manage and
publish content for a LISSA backend.

You can use it by placing the project inside the profiles directory of a clean
Drupal 8 download and running drush make.

It can be automatically setup inside a Vagrant box using the LISSA repository
available at git@git.dotprojects.be:LISSA.

## Information architecture

## API

The LISSA backend publishes content through a REST API.

### Get a list of active events

GET http://lissa.dev/api/events
