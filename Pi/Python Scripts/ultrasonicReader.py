#!/usr/bin/env python

import os
import time
import RPi.GPIO as GPIO
import datetime
import glob
import MySQLdb
from time import strftime

# Variables for MySQL
db = MySQLdb.connect(host="localhost", user="root",passwd="123", db="temp_database")
cur = db.cursor()

cloud_db = MySQLdb.connect(host="wa", user="wr",passwd="", db="")
cloud_cur = cloud_db.cursor()

def distanceRead():
  # Use BCM GPIO references
  # instead of physical pin numbers
  GPIO.setmode(GPIO.BCM)
  GPIO.setwarnings(False)

  # Define GPIO to use on Pi
  GPIO_TRIGGER = 23
  GPIO_ECHO    = 24

  # Speed of sound in cm/s at temperature
  temperature = 20
  speedSound = 33100 + (0.6*temperature)

  # Set pins as output and input
  GPIO.setup(GPIO_TRIGGER,GPIO.OUT)  # Trigger
  GPIO.setup(GPIO_ECHO,GPIO.IN)      # Echo

  # Set trigger to False (Low)
  GPIO.output(GPIO_TRIGGER, False)

  # Allow module to settle
  time.sleep(0.5)

  # Send 10us pulse to trigger
  GPIO.output(GPIO_TRIGGER, True)
  # Wait 10us
  time.sleep(0.00001)
  GPIO.output(GPIO_TRIGGER, False)
  start = time.time()

  while GPIO.input(GPIO_ECHO)==0:
    start = time.time()

  while GPIO.input(GPIO_ECHO)==1:
    stop = time.time()

  # Calculate pulse length
  elapsed = stop-start

  # Distance pulse travelled in that time is time
  # multiplied by the speed of sound (cm/s)
  distance = elapsed * speedSound

  # That was the distance there and back so halve the value
  distance = distance / 2
  return round(distance, 1)

while True:
    distanceRounded = distanceRead()
    GPIO.cleanup()
    print distanceRounded
    serialnum = "12345678R"
    datetimeWrite = (time.strftime("%Y-%m-%d ") + time.strftime("%H:%M:%S"))
    print datetimeWrite

    # Local DB write
    sql = ("""INSERT INTO distanceLog (datetime,distance) VALUES (%s,%s)""",(datetimeWrite,distanceRounded))
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
    cloud_sql = ("""INSERT INTO distanceLog (datetime,distance,SerialNumber) VALUES (%s,%s,%s)""",(datetimeWrite,distanceRounded,serialnum))
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
    
    # Reset GPIO settings
    break
