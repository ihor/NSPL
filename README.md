Non-standard PHP library
========================
Sometimes when you notice that you are not happy with PHP API you write an implementation that you like. Then you forget about it and never come back to that code again because it is not practical to use it. I decided to start collecting those implementations in Non-standard PHP library.

FAQ
===
#### What are the future plans for this project?
Well... If this repo collects 10,000 ~~likes~~ stars I may rewrite NSPL as extension.
#### Why namespaces are lowercased and often contain one or two characters?
Because it is expected that developers will type NSPL library methods very often and f\map(op::$inc, [1, 2, 3])) requires less typing than FuncTools\map(Operator::$inc, [1, 2, 3]) (and also looks better, and shorter).