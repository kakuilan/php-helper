# Changelog
All notable changes to this project will be documented in this file.

## [v0.0.8]- 2020-03-12
#### Added
- 方法`ArrayHelper::setDotKey`
- 方法`ArrayHelper::getDotKey`
- 方法`ArrayHelper::hasDotKey`

#### Fixed
- none

#### Changed
- none

#### Removed
- none

## [v0.0.7]- 2020-03-11
#### Added
- 方法`ValidateHelper::isQQ`
- 方法`ValidateHelper::isNaturalNum`
- 方法`StringHelper::passwdSafeGrade`

#### Fixed
- none

#### Changed
- none

#### Removed
- none

## [v0.0.6]- 2020-03-10
#### Added
- 方法`BaseObject::parseNamespacePath`
- 方法`BaseObject::getShortName`
- 方法`BaseObject::getNamespaceName`

#### Fixed
- none

#### Changed
- none

#### Removed
- 方法`BaseObject::getClassShortName`

## [v0.0.5]- 2020-03-10
#### Added
- 常量`Consts::DELIMITER`
- 常量`Consts::PAAMAYIM_NEKUDOTAYIM`

#### Fixed
- none

#### Changed
- none

#### Removed
- none

## [v0.0.4]- 2020-03-09
#### Added
- 方法`ValidateHelper::isMultibyte`
- 类`Kph\Exceptions\BaseException`
- 类`Kph\Exceptions\UncatchableException`

#### Fixed
- 修复`StringHelper::fixHtml`BUG,使用DOMDocument替代正则
- 修复`Concurrent\co`错误捕获

#### Changed
- rename `DirectoryHelper::emptyDir` to `DirectoryHelper::clearDir`
- 修改`Future::resolve`,支持处理value是is_callable的情况

#### Removed
- `Kph\Concurrent\Exception\UncatchableException`

## [v0.0.3]- 2020-03-06
#### Added
- 方法`StringHelper::trim`
- 方法`StringHelper::toCamelCase`
- 方法`StringHelper::toSnakeCase`
- 方法`StringHelper::toKebabCase`
- 方法`StringHelper::removeBefore`
- 方法`StringHelper::removeAfter`
- 方法`ValidateHelper::isSpace`
- 方法`ValidateHelper::isWhitespace`

#### Fixed
- none

#### Changed
- `ArrayHelper::dstrpos` 改为`StringHelper::dstrpos` 
- `StringHelper::removeSpace` 增加参数$all
- `ValidateHelper::startsWith` 增加参数$ignoreCase
- `ValidateHelper::endsWith` 增加参数$ignoreCase

#### Removed
- none

## [v0.0.2]- 2020-03-03
#### Added
- 方法`NumberHelper::geoDistance`
- 方法`StringHelper::removeEmoji`

#### Fixed
- none

#### Changed
- none

#### Removed
- none

