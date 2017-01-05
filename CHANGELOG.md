# CHANGELOG

## 1.2 / 2017-01-05

* Made ```\nspl\args``` PHP 7 compatible

## 1.1.1 / 2016-06-13

* Fixed ```Set::toArray()``` behaviour for empty sets. Thanks [Thomas Denoréaz](https://github.com/ThmX)

## 1.1 / 2016-05-19

* Improved performance
* Introduced new simpler and more flexible [\nspl\args](https://github.com/ihor/Nspl#nsplargs) API
* Added [Set](https://github.com/ihor/Nspl#set) data structure to ```\nspl\ds```
* Added the following functions to ```\nspl\a```
    * [partition](https://github.com/ihor/Nspl#partitionpredicate-sequence)
    * [span](https://github.com/ihor/Nspl#spanpredicate-sequence)
    * [flatMap](https://github.com/ihor/Nspl#flatmapfunction-sequence)
    * [zipWith](https://github.com/ihor/Nspl#zipwithfunction-sequence1-sequence2)
    * [filterNot](https://github.com/ihor/Nspl#filternotpredicate-sequence)
    * [second](https://github.com/ihor/Nspl#secondsequence)
    * [takeKeys](https://github.com/ihor/Nspl#takekeyssequence-array-keys)
    * [takeWhile](https://github.com/ihor/Nspl#takewhilepredicate-sequence)
    * [dropWhile](https://github.com/ihor/Nspl#dropwhilepredicate-sequence)
* Added the [id](https://github.com/ihor/Nspl#idvalue) function to ```\nspl\f```
* Moved [map](https://github.com/ihor/Nspl#mapfunction-sequence), [reduce](https://github.com/ihor/Nspl#reducefunction-sequence-initial--0) and [filter](https://github.com/ihor/Nspl#filterpredicate-sequence) from ```\nspl\f``` to ```\nspl\a```
* Moved [getType](https://github.com/ihor/Nspl#gettypevar) from ```\nspl\ds``` to ```\nspl```
* Renamed the following ```\nspl\a``` functions
    * ```moveElement``` to [reorder](https://github.com/ihor/Nspl#reorderarray-list-from-to)
    * ```extend``` to [merge](https://github.com/ihor/Nspl#mergesequence1-sequence2)
    * ```getByKey``` to [value](https://github.com/ihor/Nspl#valuearray-key-default--null)
* Fixed issue #1: [Strict mode warning](https://github.com/ihor/Nspl/issues/1)
* Fixed issue #4: [Strict error](https://github.com/ihor/Nspl/issues/4)
* Updated [documentation](https://github.com/ihor/Nspl#non-standard-php-library-nspl) and [examples](https://github.com/ihor/Nspl/tree/master/examples)

## 1.0.1 / 2016-01-24

* Fixed incompatibility issues with PHP 5.4 in ```\nspl\args``` and ```\nspl\rnd```
* Added argument validations to all NSPL modules


## 1.0 / 2016-01-17

* First release
