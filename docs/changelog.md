# Changelog
All notable changes to this project will be documented in this file.

## [v0.0.4]- 2020-03-09
#### Added
- `ValidateHelper::isMultibyte`
- `Kph\Exceptions\BaseException`
- `Kph\Exceptions\UncatchableException`

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
- `StringHelper::trim`
- `StringHelper::toCamelCase`
- `StringHelper::toSnakeCase`
- `StringHelper::toKebabCase`
- `StringHelper::removeBefore`
- `StringHelper::removeAfter`
- `ValidateHelper::isSpace`
- `ValidateHelper::isWhitespace`

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
- `NumberHelper::geoDistance`
- `StringHelper::removeEmoji`

#### Fixed
- none

#### Changed
- none

#### Removed
- none

