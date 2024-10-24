import serial
import mysql.connector
import serial.tools.list_ports
import time
from datetime import datetime

# The unique keywords sent by your Arduinos
TARGET_KEYWORD_TIMEIN = "timein"
TARGET_KEYWORD_TIMEOUT = "timeout"

# Function to find the Arduino by the unique keyword
def find_arduino_by_keyword(keyword):
    ports = list(serial.tools.list_ports.comports())
    for port in ports:
        try:
            # Open a temporary serial connection to each port
            ser = serial.Serial(port.device, 9600, timeout=2)
            time.sleep(2)  # Wait for the Arduino to initialize

            # Check if the Arduino sends the unique keyword
            ser.write(b'\n')  # Send a new line to trigger a response
            line = ser.readline().decode('utf-8').strip()

            if keyword in line:
                print(f"Found Arduino with keyword '{keyword}' on {port.device}")
                ser.close()
                return port.device  # Return the matching port device

            ser.close()
        except (serial.SerialException, UnicodeDecodeError) as e:
            print(f"Error reading {port.device}: {e}")

    raise Exception(f"Arduino with keyword '{keyword}' not found. Please check the connection.")

# Automatically detect the specific Arduino serial ports by keywords
SERIAL_PORT_TIMEIN = find_arduino_by_keyword(TARGET_KEYWORD_TIMEIN)
SERIAL_PORT_TIMEOUT = find_arduino_by_keyword(TARGET_KEYWORD_TIMEOUT)
BAUD_RATE = 9600

# Database connection
db = mysql.connector.connect(
    host="localhost",
    user="root",  
    password="", 
    database="gatesystem"
)
cursor = db.cursor()

# Open serial ports for both Arduinos
ser_timein = serial.Serial(SERIAL_PORT_TIMEIN, BAUD_RATE)
ser_timeout = serial.Serial(SERIAL_PORT_TIMEOUT, BAUD_RATE)
print("Listening for RFID data from both timein and timeout...")

# Helper function to check the last scan (category and datetime) for the UID
def get_last_scan(uid):
    cursor.execute(
        """
        SELECT category, datetime FROM dailylogs
        JOIN rfid ON dailylogs.stid = rfid.cid OR dailylogs.sid = rfid.cid
        WHERE rfid.uid = %s ORDER BY dailylogs.datetime DESC LIMIT 1
        """, (uid,)
    )
    return cursor.fetchone()

# Helper function to check if it's a new day compared to the last scan
def is_new_day(last_datetime):
    last_scan_date = last_datetime.date()
    current_date = datetime.now().date()
    return current_date > last_scan_date

try:
    while True:
        # Listen for data from the "timein" Arduino
        if ser_timein.in_waiting > 0:
            line = ser_timein.readline().decode('utf-8').strip()
            print(f"Received from timein: {line}")

            if line.startswith("Card UID: "):
                uid = line.split("Card UID: ")[1]
                
                # Check if UID already exists in the database
                cursor.execute("SELECT cid FROM rfid WHERE uid = %s", (uid,))
                cid_result = cursor.fetchone()

                if cid_result:
                    cid = cid_result[0]

                    # Get the last scan for this UID
                    last_scan = get_last_scan(uid)

                    if last_scan:
                        last_category, last_datetime = last_scan
                        new_day = is_new_day(last_datetime)

                        if last_category == 'OUT' or new_day:
                            # The last scan was OUT or it's a new day, allow the IN scan
                            cursor.execute(
                                "INSERT INTO dailylogs (stid, category) VALUES (%s, 'IN')",
                                (cid,)
                            )
                            print(f"Staff/Student with UID {uid} logged IN.")
                            ser_timein.write(b'SUCCESS_IN\n')
                        else:
                            # Last scan was IN, cannot log IN again without OUT
                            print(f"UID {uid} already logged IN today. Can't log IN again without OUT.")
                            ser_timein.write(b'ERROR_ALREADY_IN\n')

                    else:
                        # No previous records, allow first "IN"
                        cursor.execute(
                            "INSERT INTO dailylogs (sid, category) VALUES (%s, 'IN')",
                            (cid,)
                        )
                        print(f"First log for UID {uid}. Logged IN.")
                        ser_timein.write(b'SUCCESS_IN\n')

                    db.commit()  # Commit the transaction

                else:
                    print("No matching UID found in the RFID table.")
                    ser_timein.write(b'ERROR_NO_MATCH\n')

        # Listen for data from the "timeout" Arduino
        if ser_timeout.in_waiting > 0:
            line = ser_timeout.readline().decode('utf-8').strip()
            print(f"Received from timeout: {line}")

            if line.startswith("Card UID: "):
                uid = line.split("Card UID: ")[1]
                
                # Check if UID already exists in the database
                cursor.execute("SELECT cid FROM rfid WHERE uid = %s", (uid,))
                cid_result = cursor.fetchone()

                if cid_result:
                    cid = cid_result[0]

                    # Get the last scan for this UID
                    last_scan = get_last_scan(uid)

                    if last_scan:
                        last_category, last_datetime = last_scan

                        if last_category == 'IN':
                            # The last scan was IN, allow the OUT scan
                            cursor.execute(
                                "INSERT INTO dailylogs (stid, category) VALUES (%s, 'OUT')",
                                (cid,)
                            )
                            print(f"Staff/Student with UID {uid} logged OUT.")
                            ser_timeout.write(b'SUCCESS_OUT\n')
                        else:
                            # Last scan was OUT, cannot log OUT again without IN
                            print(f"UID {uid} already logged OUT. Can't log OUT again without IN.")
                            ser_timeout.write(b'ERROR_ALREADY_OUT\n')

                    else:
                        print(f"UID {uid} has no 'IN' record, can't log OUT.")
                        ser_timeout.write(b'ERROR_NO_IN_RECORD\n')

                    db.commit()  # Commit the transaction

                else:
                    print("No matching UID found in the RFID table.")
                    ser_timeout.write(b'ERROR_NO_MATCH\n')

except KeyboardInterrupt:
    print("Exiting...")
finally:
    ser_timein.close()
    ser_timeout.close()
    cursor.close()
    db.close()
