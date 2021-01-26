# Composer config plugin changelog

## 0.6.0 under development

- Enh #147: Add context to errors (xepozz)
- Chg #34: Better name for output config classes (samdark) 

## 0.5.0 December 24, 2020

- Chg: Changed namespace to `Yiisoft/Composer/Config` ([@samdark])
- Fix #1: Use Composer to determine vendor directory (roxblnfk)
- Enh #47: Add support for runtime environment variables via Env::get() (xepozz)
- Enh: Add Composer 2 support ([@samdark])
- Bug #21: Fix merge failures on first Composer install / update ([@samdark])
- Enh: Make base path detection more reliable (xepozz, [@samdark])
- Bug #54: Fix failure when using short closure (xepozz)
- Bug #72: Fix serialization of objects with closure (xepozz)
- Enh #70: Removed generation of aliases.php (xepozz)
- Enh #85: Rebuild now works regardless of composer dump-autoload (yiiliveext, [@samdark])
- Enh #113: Add `Builder::require('my-config')` that can be used for sub-configs ([@samdark])
- Enh #121: Clear output directory before build configuration (vjik)
- Bug #58: Add PHP 8 support ([@samdark])

## 0.4.0 March 8, 2020

- Fixed config assembling on Windows ([@samdark])
- Added configuring of output dir ([@hiqsol])
- Added building alternative configs ([@hiqsol])
- Added support for `vlucas/phpdotenv` v4 ([@jseliga])
- Better work with env vars ([@hiqsol])
- Used `riimu/kit-phpencoder` for variable exporting ([@hiqsol])
- Bug fixes ([@hiqsol], [@SilverFire], [@samdark], [@noname007], [@jomonkj], [@machour])

## 0.3.0 April 11, 2019

- Fixed config reading and merging ([@hiqsol])
- Added dev-only configs ([@hiqsol], [@samdark])
- Changed to use `defines` files as is to keep values ([@hiqsol])
- Reworked configuration files building ([@hiqsol], [@marclaporte], [@loveorigami])

## 0.2.5 May 19, 2017

- Added showing package dependencies hierarchy tree with `composer du -v` ([@hiqsol])

## 0.2.4 May 18, 2017

- Added proper resolving of config dependencies with `Resolver` class ([@hiqsol])
- Fixed exportVar closures in Windows ([@SilverFire], [@edgardmessias])

## 0.2.3 April 18, 2017

- Added vendor dir arg to `Builder::path` to get config path at given vendor dir ([@hiqsol])

## 0.2.2 April 12, 2017

- Improved README ([@hiqsol])
- Added support for `.env`, JSON and YAML ([@hiqsol])

## 0.2.1 March 23, 2017

- Fixed wrong call of `Composer\Config::get()` ([@SilverFire])

## 0.2.0 March 15, 2017

- Added initializaion of composer autoloading for project classes become usable in configs ([@hiqsol])
- Added work with `$config_name` paths for use of already built config ([@hiqsol])
- Renamed pathes -> paths everywhere ([@hiqsol])
- Added collecting dev aliases for root package ([@hiqsol])

## 0.1.0 December 26, 2016

- Added proper rebuild ([@hiqsol])
- Changed output dir to `composer-config-plugin-output` ([@hiqsol])
- Changed: splitted out `Builder` ([@hiqsol])
- Changed namespace to `hiqdev\composer\config` ([@hiqsol])

## 0.0.9 September 22, 2016

- Fixed infinite loop in case of circular dependencies in composer ([@hiqsol])

## 0.0.8 August 27, 2016

- Added showing ordered list of packages when verbose option ([@hiqsol])

## 0.0.7 August 26, 2016

- Fixed packages processing order again, used original `composer.json` ([@hiqsol])

## 0.0.6 August 24, 2016

- Fixed packages processing order ([@hiqsol])

## 0.0.5 June 22, 2016

- Added multiple defines ([@hiqsol])

## 0.0.4 May 21, 2016

- Added multiple configs and params ([@hiqsol])

## 0.0.3 May 20, 2016

- Changed aliases assembling ([@hiqsol])

## 0.0.2 May 19, 2016

- Removed replace composer-extension-plugin ([@hiqsol])

## 0.0.1 May 18, 2016

- Added basics ([@hiqsol])

[@SilverFire]: https://github.com/SilverFire
[d.naumenko.a@gmail.com]: https://github.com/SilverFire
[@tafid]: https://github.com/tafid
[andreyklochok@gmail.com]: https://github.com/tafid
[@BladeRoot]: https://github.com/BladeRoot
[bladeroot@gmail.com]: https://github.com/BladeRoot
[@hiqsol]: https://github.com/hiqsol
[sol@hiqdev.com]: https://github.com/hiqsol
[@edgardmessias]: https://github.com/edgardmessias
[edgardmessias@gmail.com]: https://github.com/edgardmessias
[@samdark]: https://github.com/samdark
[sam@rmcreative.ru]: https://github.com/samdark
[@loveorigami]: https://github.com/loveorigami
[loveorigami@mail.ru]: https://github.com/loveorigami
[@marclaporte]: https://github.com/marclaporte
[marc@laporte.name]: https://github.com/marclaporte
[@jseliga]: https://github.com/jseliga
[seliga.honza@gmail.com]: https://github.com/jseliga
[@machour]: https://github.com/machour
[machour@gmail.com]: https://github.com/machour
[@jomonkj]: https://github.com/jomonkj
[jomon.entero@gmail.com]: https://github.com/jomonkj
[@noname007]: https://github.com/noname007
[soul11201@gmail.com]: https://github.com/noname007
