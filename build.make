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

; Download the install profile and recursively build all its dependencies:
projects[lissa_kickstart][type] = profile
projects[lissa_kickstart][download][type] = git
projects[lissa_kickstart][download][url] = git@github.com:Crosscheck/lissa_kickstart.git

; Overwrite an existing build;
projects[lissa_kickstart][overwrite] = TRUE

; Use a branch instead of a tag for development. It's advisable to use a tag
; of a fixed version when packaging for production.
projects[lissa_kickstart][download][branch] = 8.0.x

; Keep the git directory so this build can be used for local development. It's
; advisable to delete this when packaging for production.
projects[lissa_kickstart][download][working-copy] = TRUE
