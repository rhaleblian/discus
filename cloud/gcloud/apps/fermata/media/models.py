# This is an auto-generated Django model module.
# You'll have to do the following manually to clean this up:
#     * Rearrange models' order
#     * Make sure each model has one field with primary_key=True
# Feel free to rename the models, but don't rename db_table values or field names.
#
# Also note: You'll have to insert the output of 'django-admin.py sqlcustom [appname]'
# into your database.

from django.db import models

class Disc(models.Model):
    id = models.IntegerField(primary_key=True)
    label = models.CharField(max_length=384, blank=True)
    name = models.CharField(max_length=135, blank=True)
    format = models.CharField(max_length=9, blank=True)
    status = models.IntegerField(null=True, blank=True)
    class Meta:
        db_table = u'disc'

class DiscStatusDescription(models.Model):
    id = models.IntegerField(primary_key=True)
    description = models.CharField(max_length=135, blank=True)
    class Meta:
        db_table = u'disc_status_description'

class File(models.Model):
    id = models.IntegerField(primary_key=True)
    disc_id = models.IntegerField(null=True, blank=True)
    dir = models.CharField(max_length=1536, blank=True)
    name = models.CharField(max_length=384, blank=True)
    mtime = models.DateTimeField(null=True, blank=True)
    bytes = models.IntegerField(null=True, blank=True)
    class Meta:
        db_table = u'file'

class FileView(models.Model):
    disc_label = models.CharField(max_length=384, blank=True)
    disc_name = models.CharField(max_length=135, blank=True)
    disc_format = models.CharField(max_length=9, blank=True)
    disc_status = models.IntegerField(null=True, blank=True)
    name = models.CharField(max_length=384, blank=True)
    dir = models.CharField(max_length=1536, blank=True)
    mtime = models.DateTimeField(null=True, blank=True)
    bytes = models.IntegerField(null=True, blank=True)
    class Meta:
        db_table = u'file_view'

