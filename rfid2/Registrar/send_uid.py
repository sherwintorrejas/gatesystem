import serial
import mysql.connector
import serial.tools.list_ports
import time
import os
# The unique keyword sent by your Arduino
# TARGET_KEYWORD = "register"

# # Function to find the Arduino by the unique keyword
# def find_arduino_by_keyword():
#     ports = list(serial.tools.list_ports.comports())
#     for port in ports:
#         try:
#             # Open a temporary serial connection to each port
#             ser = serial.Serial(port.device, 9600, timeout=2)
#             time.sleep(2)  # Wait for the Arduino to initialize

#             # Check if the Arduino sends the unique keyword
#             ser.write(b'\n')  # Send a new line to trigger a response
#             line = ser.readline().decode('utf-8').strip()

#             if TARGET_KEYWORD in line:
#                 print(f"Found Arduino with keyword '{TARGET_KEYWORD}' on {port.device}")
#                 ser.close()
#                 return port.device  # Return the matching port device

#             ser.close()
#         except (serial.SerialException, UnicodeDecodeError) as e:
#             # Skip ports that are not accessible or do not provide the expected data
#             print(f"Error reading {port.device}: {e}")
    
#     raise Exception(f"Arduino with keyword '{TARGET_KEYWORD}' not found. Please check the connection.")

# # Automatically detect the specific Arduino serial port by the keyword
# SERIAL_PORT = find_arduino_by_keyword()
SERIAL_PORT = 'COM5'
BAUD_RATE = 9600

# Database connection
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="gatesystem"
)
cursor = db.cursor()

# Open the serial port
ser = serial.Serial(SERIAL_PORT, BAUD_RATE)
print("Listening for RFID data...")

try:
    while True:
        if ser.in_waiting > 0:
            line = ser.readline().decode('utf-8').strip()
            print(f"Received: {line}")

            if line.startswith("UID: "):
                uid = line.split("UID: ")[1]
                
                # Check if UID already exists in the database
                cursor.execute("SELECT * FROM rfid WHERE uid = %s", (uid,))
                result = cursor.fetchone()
                
                if result is None:
                    # UID doesn't exist, insert it into the database
                    sql = "INSERT INTO rfid (uid, timedate) VALUES (%s, NOW())"
                    cursor.execute(sql, (uid,))
                    db.commit()
                    print(f"Stored UID: {uid}")
                    
                    # Send success message to Arduino
                    ser.write(b'SUCCESS\n')
                else:
                    print(f"UID {uid} already exists, skipping insert.")
                    
                    # Send error message to Arduino
                    ser.write(b'ERROR_DUPLICATE\n')

except KeyboardInterrupt:
    print("Exiting...")
finally:
    ser.close()
    cursor.close()
    db.close()
