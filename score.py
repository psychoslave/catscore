#! /usr/bin/env python
# -*- coding: utf-8 -*-

import wikipedia
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
    scores = []
    for page in pages:
        scores.append((page, readability_score(page)))
    scores.sort(key=lambda s: s[1])

    return scores

if __name__ == "__main__":
    categories = sys.argv
    del categories[0]
    for category in categories:
        scores = get_scored_table(category)
        for page, score in scores:
            print page, score
        print '---'
