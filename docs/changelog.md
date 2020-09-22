# Changelog
All notable changes to this project will be documented in this file.

## [v0.2.2]- 2020-09-22
#### Added
- none

#### Fixed
- none

#### Changed
- 修改`ArrayHelper::cutItems`,增加参数`$keepKey`是否保留键名

#### Removed
- none

## [v0.2.1]- 2020-09-17
#### Added
- none

#### Fixed
- 修复`Kph\Concurrent\makeClosureFun`

#### Changed
- none

#### Removed
- none

## [v0.2.0]- 2020-06-20
#### Added
- none

#### Fixed
- none

#### Changed
- 优化`EncryptHelper::authcode`

#### Removed
- none

## [v0.1.9]- 2020-05-20
#### Added
- none

#### Fixed
- 修复`StringHelper::toCamelCase`当输入首字母大写的驼峰字符串失败问题

#### Changed
- none

#### Removed
- none

## [v0.1.8]- 2020-05-19
#### Added
- none

#### Fixed
- none

#### Changed
- 方法`ValidateHelper::isIndexArray`参数不限制类型
- 方法`ValidateHelper::isAssocArray`参数不限制类型
- 方法`ValidateHelper::isOneDimensionalArray`参数不限制类型

#### Removed
- none

## [v0.1.7]- 2020-05-18
#### Added
- 方法`ValidateHelper::isOneDimensionalArray`

#### Fixed
- none

#### Changed
- none

#### Removed
- none

## [v0.1.6]- 2020-05-18
#### Added
- none

#### Fixed
- 修复`BaseObject::getClassMethods`当$filter为null时,在php7.2下失败的问题

#### Changed
- none

#### Removed
- none

## [v0.1.5]- 2020-05-18
#### Added
- 方法`ArrayHelper::regularSort`
- 方法`BaseObject::formatNamespace`
- 方法`BaseObject::getClass`
- 方法`BaseObject::getClassMethods`
- 方法`ValidateHelper::isEqualArray`

#### Fixed
- 修复`ValidateHelper::isIndexArray`存在负数索引时的问题

#### Changed
- none

#### Removed
- none

## [v0.1.4]- 2020-05-17
#### Added
- 方法`NumberHelper::randFloat`

#### Fixed
- none

#### Changed
- none

#### Removed
- none

## [v0.1.3]- 2020-05-16
#### Added
- 方法`ValidateHelper::isAssocArray`
- 方法`ValidateHelper::isIndexArray`
- 方法`ArrayHelper::compareSchema`

#### Fixed
- none

#### Changed
- none

#### Removed
- none

## [v0.1.2]- 2020-05-08
#### Added
- 方法`DateHelper::startOfDay`
- 方法`DateHelper::endOfDay`
- 方法`DateHelper::startOfMonth`
- 方法`DateHelper::endOfMonth`
- 方法`DateHelper::startOfYear`
- 方法`DateHelper::endOfYear`
- 方法`DateHelper::startOfWeek`
- 方法`DateHelper::endOfWeek`

#### Fixed
- none

#### Changed
- none

#### Removed
- none

## [v0.1.1]- 2020-04-28
#### Added
- none

#### Fixed
- 修复`ValidateHelper::isNaturalNum`为0时错误

#### Changed
- none

#### Removed
- none

## [v0.1.0]- 2020-04-09
#### Added
- 方法`NumberHelper::numberForma`
- 方法`OsHelper::isCliMode`
- 方法`StringHelper::contains`
- 方法`StringHelper::middle`
- 方法`StringHelper::uuidV4`
- 方法`ValidateHelper::isAlpha`
- 方法`ValidateHelper::isAlphaChinese`
- 方法`ValidateHelper::isAlphaNum`
- 方法`ValidateHelper::isAlphaNumChinese`
- 方法`ValidateHelper::isAlphaNumDash`
- 方法`ValidateHelper::isAlphaNumDashChinese`

#### Fixed
- none

#### Changed
- none

#### Removed
- none

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

