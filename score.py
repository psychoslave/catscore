#! /usr/bin/env python
# -*- coding: utf-8 -*-

import wikipedia
import operator # for dummy sorting

sample = 'Philosophers'

# help(cat_page)

def getPagesIn(category):
	"""Should return the list of pages that the provided category contains"""
	page_name = 'Category:' + category
	page = wikipedia.page(page_name)
	return page.links

def readability_score(page):
    summary = wikipedia.summary(page)
    return 100./(len(summary)+1) # the shorter the better, but avoid zero

pages = getPagesIn(sample)
scores = dict()
for page in pages:
    scores[page] = readability_score(page)

sorted_scores = sorted(scores.items(), key=operator.itemgetter(1))
for page, score in sorted_scores:
    print page, score
"""
    """
