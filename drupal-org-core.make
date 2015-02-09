; Base make file for building the LISSA Kickstart distribution.
;
; When executed with drush make this file will download the LISSA drupal profile
; and all its dependencies.

core = "8.x"
api = 2

projects[drupal][overwrite] = TRUE
projects[drupal][download][type] = git
; projects[drupal][download][tag] = 8.0.0-beta4
projects[drupal][download][revision] = 73069e05e8a32f421e0f9a3132ae76d94f3bdcde
projects[drupal][download][branch] = 8.0.x
