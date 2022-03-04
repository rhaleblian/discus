#!/usr/bin/env python
""" Entry point for Coda web client.
"""
import json
#from google.appengine.api import urlfetch
import urllib2
import webapp2

top_level_url = "https://portal.disneyanimation.com"
html1 = '''<!doctype html>
<html lang="en">
'''
head = '''
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Simple Mobile Listview</title>
    <meta name="author" content="Jake Rocheleau">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="HandheldFriendly" content="true">
        <meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
    <script type="text/javascript" src="retina.js"></script>
<!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
</head>
'''
body1 = '''
<body>
    <div id="view">
        <header>
            <h1>Graphs</h1>
        </header>

        <div id="container">
            <ul>
'''
body2 = '''
            </ul>
        </div>
    </div>
<script type="text/javascript">
$(document).ready(function(){
    $("a").on("click", function(e){
        e.preventDefault();
    });
});
</script>
</body>'''
html2 = '</html>'


class MainHandler(webapp2.RequestHandler):
    def get(self):
        install_authenticator()
        url = '%s/icoda4/user/rhalebli' % top_level_url
        try:
            result = urllib2.urlopen(url)
            self.do_something_with_result(result)
        except urllib2.URLError, e:
            self.handle_error(e)

    def fetch_with_alternate_auth(self, url):
        result = urlfetch.fetch(url, validate_certificate=False)
        #self.response.write(result)
        self.do_something_with_result(result.content)

    def do_something_with_result(self, result):
        self.response.write(html1)
        self.response.write(head)
        self.response.write(body1)

        response = result.readlines()
        obj = json.loads(''.join(response))
        graphs = obj['dgraph']
        for graph in graphs:
            self.response.write('<li class="clearfix">')
            entry = '''%s %s %s %s %s<br/>
            %s''' % (graph['did'], graph['_queued'],graph['_running'], graph['_exit'], graph['_done'],
                     graph['title'])
            self.response.write(entry)
            self.response.write('</li>')

        self.response.write('</body>')
        self.response.write(html2)

    def handle_error(self, e):
        self.response.write(str(e))
        self.response.write('<pre>%s</pre>' % e.headers)


def install_authenticator():
    """ This is obviously temp, don't gripe
        Auth should be via Google Apps signin """
    username = 'rhalebli'
    password = 'saZZac33'
    password_mgr = urllib2.HTTPPasswordMgrWithDefaultRealm()
    password_mgr.add_password(None, top_level_url, username, password)
    handler = urllib2.HTTPBasicAuthHandler(password_mgr)
    opener = urllib2.build_opener(handler)
    urllib2.install_opener(opener)


app = webapp2.WSGIApplication([
    ('/', MainHandler)
], debug=True)


__copyright__ = """
#
# Copyright 2007 Google Inc.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#
"""