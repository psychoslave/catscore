This is a project implementing scoring of Wikipedia pages within a category
as suggested on this [task description](https://www.mediawiki.org/wiki/User:Kaldari/Task_2).

The first implementation only makes a prototype with minimal structure : sure pywikibot and ORES immediately come to mind, but I didn't used one of it yet, so trying to have a first draft with things smaller seems a good idea. :)

Let's first make a command line script, and see later to add a webinterface.

[mediawiki-utilities](https://pypi.python.org/pypi/mediawiki-utilities/0.2.1) seemed interesting, but maybe yet too advanced for prototyping as it looks like it makes session management.

So, the module [wikipedia](https://pypi.python.org/pypi/wikipedia) should be enough for a first draft.
