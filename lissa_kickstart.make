core = 8.x
api = 2

projects[devel][type] = "module"
projects[devel][subdir] = "contrib"
projects[devel][download][type] = git
projects[devel][download][revision] =  777465c7860a70d1ad4a16fc66d23c3e827cfb4b
projects[devel][download][branch] = 8.x-1.x

projects[composer_manager][type] = "module"
projects[composer_manager][subdir] = "contrib"
projects[composer_manager][download][type] = git
projects[composer_manager][download][revision] =  b1510ee6560d76e69155dc49b69ec0cd552fc949
projects[composer_manager][download][branch] = 8.x-1.x
projects[composer_manager][patch][] = "http://www.drupal.org/files/issues/composer_manager-remove-url-calls-2347559-9.patch"

projects[config_devel][type] = "module"
projects[config_devel][subdir] = "contrib"
projects[config_devel][version] = 1.0-alpha14

