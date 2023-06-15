# Changelog

All notable changes to this project will be documented in this file.

## [v0.4.9]- 2023-06-15

#### Added

- none

#### Fixed

- none

#### Changed

- 修改`BaseService::$result`类型为mixed

#### Removed

- none

## [v0.4.8]- 2023-06-06

#### Added

- none

#### Fixed

- 修复`StrictObject::isset`

#### Changed

- 优化`DateHelper::timestamp`
- 优化`DateHelper::isBetween`
- 优化`StringHelper::passwdSafeGrade`

#### Removed

- 移除`DateHelper::dateTime`
- 移除`DateHelper::yearMonth`
- 移除`DateHelper::monthDay`

## [v0.4.7]- 2023-05-04

#### Added

- 新增`ArrayHelper::xmlToArray`
- 新增`ArrayHelper::arrayToXml`
- 新增`DateHelper::timestamp`
- 新增`DateHelper::year`
- 新增`DateHelper::month`
- 新增`DateHelper::day`
- 新增`DateHelper::hour`
- 新增`DateHelper::minute`
- 新增`DateHelper::second`
- 新增`DateHelper::yearMonth`
- 新增`DateHelper::monthDay`
- 新增`DateHelper::format`
- 新增`DateHelper::dateTime`
- 新增`DateHelper::isBetween`

#### Fixed

- none

#### Changed

- none

#### Removed

- none

## [v0.4.6]- 2023-02-28

#### Added

- 新增`BaseService::setResult`
- 新增`BaseService::getResult`

#### Fixed

- none

#### Changed

- none

#### Removed

- none

## [v0.4.5]- 2022-04-01

#### Added

- none

#### Fixed

- 优化`EncryptHelper::authcode`

#### Changed

- none

#### Removed

- none

## [v0.4.4]- 2022-01-14

#### Added

- 新增`UrlHelper::getSiteUrl`,根据网址获取站点URL

#### Fixed

- none

#### Changed

- 将`OsHelper::getDomain`移动到`UrlHelper::getDomain`
- 将`OsHelper::getUrl`移动到`UrlHelper::getUrl`
- 将`OsHelper::getUri`移动到`UrlHelper::getUri`

#### Removed

- none

## [v0.4.3]- 2022-01-13

#### Added

- none

#### Fixed

- none

#### Changed

- 兼容php 8.0

#### Removed

- none

## [v0.4.2]- 2021-12-09

#### Added

- none

#### Fixed

- none

#### Changed

- 修改常量`DELIMITER`

#### Removed

- none

## [v0.4.1]- 2021-10-24

#### Added

- 增加`StringHelper::toBytes`
- 增加`StringHelper::bytes2Str`

#### Fixed

- none

#### Changed

- none

#### Removed

- none

## [v0.4.0]- 2021-04-13

#### Added

- 增加`ValidateHelper::startsWiths`
- 增加`ValidateHelper::endsWiths`

#### Fixed

- none

#### Changed

- none

#### Removed

- none

## [v0.3.9]- 2021-02-23

#### Added

- 增加`OsHelper::remoteFileExists`

#### Fixed

- none

#### Changed

- none

#### Removed

- none

## [v0.3.8]- 2021-01-11

#### Added

- 增加`OsHelper::isSsl`

#### Fixed

- none

#### Changed

- none

#### Removed

- none

## [v0.3.7]- 2021-01-05

#### Added

- 增加`OsHelper::isAjax`

#### Fixed

- none

#### Changed

- none

#### Removed

- none

## [v0.3.6]- 2020-12-19

#### Added

- none

#### Fixed

- none

#### Changed

- 修改`ArrayHelper::searchItem`支持可迭代的对象
- 修改`ArrayHelper::searchMutil`支持可迭代的对象

#### Removed

- none

## [v0.3.5]- 2020-12-18

#### Added

- none

#### Fixed

- none

#### Changed

- 修改`ArrayHelper::searchItem`支持数组元素是对象
- 修改`ArrayHelper::searchMutil`支持数组元素是对象

#### Removed

- none

## [v0.3.4]- 2020-12-10

#### Added

- 添加`OsHelper::runCommand`
- 添加`ValidateHelper::isMacAddress`
- 添加`Kph\Util\MacAddress`

#### Fixed

- none

#### Changed

- 修改`DebugHelper::errorLogHandler`临时目录
- 修改`DirectoryHelper::formatDir`,兼容windows路径
- 修改`OsHelper::getPhpPath`,兼容windows

#### Removed

- none

## [v0.3.3]- 2020-12-8

#### Added

- none

#### Fixed

- none

#### Changed

- 规范BaseService错误信息类型

#### Removed

- none

## [v0.3.2]- 2020-12-2

#### Added

- 方法`EncryptHelper::opensslEncrypt`
- 方法`EncryptHelper::opensslDecrypt`

#### Fixed

- none

#### Changed

- none

#### Removed

- none

## [v0.3.1]- 2020-11-25

#### Added

- 方法`ConvertHelper::hex2Str`
- 方法`ConvertHelper::str2hex`

#### Fixed

- none

#### Changed

- move `ArrayHelper::array2Object` to `ConvertHelper::array2Object`
- move `ArrayHelper::object2Array` to `ConvertHelper::object2Array`

#### Removed

- none

## [v0.3.0]- 2020-11-21

#### Added

- 方法`NumberHelper::money2Yuan`
- 方法`NumberHelper::nearLogarithm`
- 方法`NumberHelper::splitNaturalNum`
- 方法`OsHelper::getOS`
- 方法`OsHelper::isMac`
- 方法`StringHelper::grabBrackets`
- 方法`StringHelper::stripBrackets`
- 方法`StringHelper::toArray`

#### Fixed

- 修改`BaseService::getError`为null时类型错误

#### Changed

- 修改`FileHelper::img2Base64`,增加图片类型
- 修改`StringHelper::dstrpos`,使用mb_strpos

#### Removed

- none

## [v0.2.9]- 2020-11-16

#### Added

- 方法`FileHelper::formatPath`
- 方法`FileHelper::getAbsPath`
- 方法`FileHelper::getRelativePath`
- 方法`ValidateHelper::isNaturalRange`

#### Fixed

- none

#### Changed

- 修改`DirectoryHelper::formatDir`,允许特殊字符

#### Removed

- none

## [v0.2.8]- 2020-10-31

#### Added

- 方法`DateHelper::startOfHour`
- 方法`DateHelper::endOfHour`

#### Fixed

- none

#### Changed

- none

#### Removed

- none

## [v0.2.7]- 2020-10-31

#### Added

- none

#### Fixed

- 修复`ArrayHelper::object2Array`当对象内嵌对象时不转换问题

#### Changed

- none

#### Removed

- none

## [v0.2.6]- 2020-10-19

#### Added

- 新增`NumberHelper::numberSub`数值截取方法

#### Fixed

- none

#### Changed

- 修改`NumberHelper::numberFormat`,去掉参数`$decPoint`和`$thousandssep`

#### Removed

- none

## [v0.2.5]- 2020-10-15

#### Added

- none

#### Fixed

- none

#### Changed

- 修改`DirectoryHelper::getFileTree`,弃用`glob`函数

#### Removed

- none

## [v0.2.4]- 2020-09-25

#### Added

- none

#### Fixed

- none

#### Changed

- 修改`ArrayHelper::regularSort`,增加参数`$recursive`是否递归

#### Removed

- none

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

