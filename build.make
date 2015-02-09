; Base make file for building the LISSA Kickstart distribution.
;
; When executed with drush make this file will download the LISSA drupal profile
; and all its dependencies.

core = "8.x"
api = 2

includes[] = drupal-org-core.make

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
