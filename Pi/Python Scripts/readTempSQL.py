#!/usr/bin/env python

import os
import time
import datetime
import glob
import MySQLdb
from time import strftime
 
os.system('modprobe w1-gpio')
os.system('modprobe w1-therm')
temp_sensor = '/sys/bus/w1/devices/28-031671cf0fff/w1_slave'
 
# Variables for MySQL
db = MySQLdb.connect(host="localhost", user="root",passwd="123", db="temp_database")
cur = db.cursor()

cloud_db = MySQLdb.connect(host="wm", user="war",passwd="", db="")
cloud_cur = cloud_db.cursor()
 
def tempRead():
    t = open(temp_sensor, 'r')
    lines = t.readlines()
    t.close()
 
    temp_output = lines[1].find('t=')
    if temp_output != -1:
        temp_string = lines[1].strip()[temp_output+2:]
        temp_c = float(temp_string)/1000.0
    return round(temp_c,1)
 
while True:
    temp = tempRead()
    print temp
    datetimeWrite = (time.strftime("%Y-%m-%d ") + time.strftime("%H:%M:%S"))
    serialnum = "12345678R"
    print datetimeWrite

    # Local DB write
    sql = ("""INSERT INTO tempLog (datetime,temperature) VALUES (%s,%s)""",(datetimeWrite,temp))
    try:
        print "Writing to database..."
        # Execute the SQL command
        cur.execute(*sql)
        # Commit your changes in the database
        db.commit()
        print "Write Complete"
 
    except:
        # Rollback in case there is any error
        db.rollback()
        print "Failed writing to database"
 
    cur.close()
    db.close()

    # Cloud DB write
    cloud_sql = ("""INSERT INTO tempLog (datetime,temperature,SerialNumber) VALUES (%s,%s,%s)""",(datetimeWrite,temp,serialnum))
    try:
        print "Writing to database..."
        # Execute the SQL command
        cloud_cur.execute(*cloud_sql)
        # Commit your changes in the database
        cloud_db.commit()
        print "Write Complete"
 
    except:
        # Rollback in case there is any error
        cloud_db.rollback()
        print "Failed writing to database"
 
    cloud_cur.close()
    cloud_db.close()
    break
