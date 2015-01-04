""" Munging web services.
"""
#import pprint


def gdata_cell_values():
    """ Talk to Google Data API to get cell values.
    """
    import gdata.spreadsheets.client
    import simplejson
    import os

    conf_root = os.getenv('USERPROFILE') or os.getenv('HOME')
    conf_path = os.path.join(conf_root, 'config.json')
    conf = simplejson.load(open(conf_path))
    key = conf.get('spreadsheet_key')

    client = gdata.spreadsheets.client.SpreadsheetsClient()
    token = client.ClientLogin(conf.get('user'), conf.get('password'), 'me')
    sheet = client.GetWorksheet(key, 1)
    cq = gdata.spreadsheets.client.CellQuery(3, 3, 1, 3)
    cs = client.GetCells(key, 1, q=cq)
    for i in range(0, 3):
        cell_ent = cs.entry[i]
        print cell_ent.cell.input_value  # get a current cell value


def skreeeeek(do_move=False):
    """
    Scrape HTML from Shoeboxed add documents page, to get full image filenames.
    Optionally move images to processing cubicle.
    """
    import os
    import shutil
    from xml.etree import cElementTree

    filename = 'D:/Dropbox/Pictures/Receipt/snippet.html'

    tree = cElementTree.parse(filename)
    root = tree.getroot()
    elements = root.findall('./ul/li/div')
    names = [e.get('title') for e in elements if e.get('title') is not None]

    def move(names):
        d_src = 'D:/Dropbox/Pictures/Receipt/'
        d_dst = 'D:/Dropbox/Pictures/Receipt/Processing/'
        for n in names:
            shutil.move(d_src + n, d_dst + n)

    print(names)
    if do_move:
        move(names)


__author__ = 'rhaleblian'
