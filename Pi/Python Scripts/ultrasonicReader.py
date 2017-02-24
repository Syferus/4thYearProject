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

def distanceRead():
  # Use BCM GPIO references
  # instead of physical pin numbers
  GPIO.setmode(GPIO.BCM)

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