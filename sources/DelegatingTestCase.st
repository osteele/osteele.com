"Changes in change set DelegatingTestCase"
'From VisualWorks® Non-Commercial, Release 3.0 of May 8, 1998 on September 13, 1999 at 3:19:01 pm'!

!TestSuite class methodsFor: 'instance creation'!

forClass: aClass 
	^(TestSuite named: aClass name)
		addTestCases: ((aClass class organization listAtCategoryNamed: #'test cases') collect: 
						[:selector | 
						DelegatingTestCase target: aClass selector: selector]);
		yourself! !


TestCase subclass: #DelegatingTestCase
	instanceVariableNames: 'target '
	classVariableNames: ''
	poolDictionaries: ''
	category: 'Testing Framework'!


!DelegatingTestCase methodsFor: 'running'!

performTest
	target perform: selector! !

!DelegatingTestCase methodsFor: 'printing'!

printOn: aStream
	aStream nextPutAll: 'Test case '.
	target printOn: aStream.
	aStream
		nextPutAll: '>>';
		nextPutAll: selector! !

!DelegatingTestCase methodsFor: 'private'!

setTarget: anObject
	target := anObject! !
"-- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- "!

DelegatingTestCase class
	instanceVariableNames: ''!


!DelegatingTestCase class methodsFor: 'instance creation'!

target: anObject selector: aSymbol
	^self new setTarget: anObject; setSelector: aSymbol; yourself! !



