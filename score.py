#! /usr/bin/env python
# -*- coding: utf-8 -*-

import wikipedia
import operator  # for dummy sorting
import sys

sample = 'Philosophers'

# help(cat_page)


def getPagesIn(category):
    """Should return the list of pages that the provided category contains"""
    page_name = 'Category:' + category
    page = wikipedia.page(page_name)
    return page.links


def readability_score(page):
    summary = wikipedia.summary(page)
    return 100./(len(summary)+1)  # the shorter the better, but avoid zero


def get_scored_table(category):
    print category
    pages = getPagesIn(category)
    scores = dict()
    for page in pages:
        scores[page] = readability_score(page)

    sorted_scores = sorted(scores.items(), key=operator.itemgetter(1))
    for page, score in sorted_scores:
        print page, score

if __name__ == "__main__":
    categories = sys.argv
    del categories[0]
    for category in categories:
        get_scored_table(category)
        print '---'
