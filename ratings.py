from datetime import datetime, date, timedelta

#MUST install mysql-python
#To do that just run "pip install mysql-python"
import MySQLdb

#DB Settings
DB_HOST = "localhost"
DB_USER = ""
DB_PASSWD = ""
DB_NAME = ""

stations = []
times = []

print "------Created empty Lists------"

#current time
t_now = datetime.now()
t_old = t_now - timedelta(days=1)
for y in range(30,1470,30):
    times.append(int((t_old + timedelta(minutes=y)).strftime('%s')))

print "------Created date ranges------"

#Connection to Db
mysql_connection = MySQLdb.connect(DB_HOST, DB_USER, DB_PASSWD, DB_NAME)
mysql_cur = mysql_connection.cursor()

print "------Created Cursor------"

#Getting a list of stations
sql = """select `id` from stations"""
mysql_cur.execute(sql)
stations_out = mysql_cur.fetchall()
for x in stations_out:
    stations.append(x[0])

print "------Created Stations------"

#Creating Ratings Table
#TODO:Discuss addition of fields
sql_table_ratings = """\
CREATE TABLE IF NOT EXISTS ratings (
  id int(11) unsigned NOT NULL auto_increment,
  station_id int(11) default NULL,
  avgAvailableDocks DECIMAL(6,4) default NULL,
  avgAvailableBikes DECIMAL(6,4) default NULL,
  timestamp int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;"""

mysql_cur.execute(sql_table_ratings)
print "------Created Ratings Table------"

#Aggregating ratings
#Currently averages for availableBikes and availableDocks
#TODO: Fix PEP-8 after Demo
for i in stations:
    for j in range(0, len(times)-1):
        sql1 = """select %s, avg(`availableBikes`), avg(`availableDocks`),
        %s from station_status where `timestamp` > %s and `timestamp` <  %s and
        `station_id` = %s""" % (i,times[j+1],times[j],times[j+1],i)

        print "Calculating for Station:" + str(i)

        mysql_cur.execute(sql1)
        ans = mysql_cur.fetchone()
        if ans[1]:
            sql2 = """insert into ratings(id, station_id, avgAvailableBikes,
            avgAvailableDocks, timestamp)values (NULL, %s, %s, %s, %s)""" % (ans[0], ans[1],
            ans[2], ans[3])
            mysql_cur.execute(sql2)
