""" Talk to Google Data API to get cell values.
"""

import gdata.spreadsheets.client
import simplejson
import os
import pprint

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


__author__ = 'rhaleblian'
