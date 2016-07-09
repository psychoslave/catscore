#! /usr/bin/env python
# -*- coding: utf-8 -*-

import wikipedia
import sys
import os

sample = 'Philosophers'

# help(cat_page)


def getPagesIn(category):
    """Return the list of pages contained in the provided category"""
    # wikipedia module doesn't provide category listing, let's retrieve it
    # with some quick & dirty shell command for now
    cmd = ("wget 'https://en.wikipedia.org/wiki/Category:"
           "%(category)s'"
           " --quiet -O - "  # use standard output quietly
           "|grep '<li>' "  # get only line with list element
           "|grep -v Category"  # exclude subcategory
           "|sed -e 's/.*title=\"//' -e 's/\">.*//'"  # extract title
           "") % locals()
    fetched_titles = os.popen(cmd)
    pages = []
    for line in fetched_titles:
        pages.append(line[:-1])

    return pages


def readability_score(page):
    """Return a readability score for the given page title"""
    summary = wikipedia.summary(page)
    return 100./(len(summary)+1)  # the shorter the better, but avoid zero


def get_scored_table(category):
    """Return an array of (title, score) tuples for pages in category"""
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
