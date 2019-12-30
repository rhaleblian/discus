form yoyodyne import media


def dumpnosql(table_name):
    sql = """select * from %s;""" % table_name
    print sql
    connection = media.connect()
    cursor = connection.cursor()
    rows = cursor.execute(sqlalchemy.text(sql))
    if not rows:
        return
    rows = cursor.fetchall()
    items = []
    for row in rows:
        print row
