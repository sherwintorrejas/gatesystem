import serial
import mysql.connector
import time
from datetime import datetime

SERIAL_PORT = 'COM6'
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
print("Listening for RFID data for OUT...")

# Function to get last log entry for a given ID
def get_last_log_id(cid):
    cursor.execute(
        "SELECT category, datetime FROM dailylogs WHERE stid = %s OR sid = %s ORDER BY datetime DESC LIMIT 1",
        (cid, cid)
    )
    return cursor.fetchone()

try:
    while True:
        if ser.in_waiting > 0:
            line = ser.readline().decode('utf-8').strip()
            print(f"Received: {line}")

            if line.startswith("Card UID: "):
                uid = line.split("Card UID: ")[1]
                
                # Check if UID exists in the database
                cursor.execute("SELECT cid FROM rfid WHERE uid = %s", (uid,))
                cid_result = cursor.fetchone()

                if cid_result:
                    cid = cid_result[0]

                    # Check in registaff table
                    cursor.execute("SELECT stid FROM registaff WHERE cid = %s", (cid,))
                    staff_result = cursor.fetchone()

                    # Check in registudent table
                    cursor.execute("SELECT sid FROM registudent WHERE cid = %s", (cid,))
                    student_result = cursor.fetchone()

                    # Determine the category and log entry
                    if staff_result:
                        stid = staff_result[0]
                        last_log = get_last_log_id(stid)

                        # Logic for logging OUT
                        if last_log:
                            last_category, last_datetime = last_log
                            current_date = datetime.now().date()

                            if last_category == 'OUT':
                                print("Cannot log OUT again without logging IN first.")
                                ser.write(b'ERROR_ALREADY_OUT\n')
                            else:
                                cursor.execute(
                                    "INSERT INTO dailylogs (stid, category) VALUES (%s, 'OUT')",
                                    (stid,)
                                )
                                print(f"Staff ID {stid} logged OUT.")
                                ser.write(b'SUCCESS_OUT\n')

                        else:
                            print("Cannot log OUT without a prior IN entry.")
                             
                            ser.write(b'ERROR_NO_IN_RECORD\n')

                    elif student_result:
                        sid = student_result[0]
                        last_log = get_last_log_id(sid)

                        # Logic for logging OUT
                        if last_log:
                            last_category, last_datetime = last_log
                            current_date = datetime.now().date()

                            if last_category == 'OUT':
                                print("Cannot log OUT again without logging IN first.")
                                ser.write(b'ERROR_ALREADY_OUT\n')
                            else:
                                cursor.execute(
                                    "INSERT INTO dailylogs (sid, category) VALUES (%s, 'OUT')",
                                    (sid,)
                                )
                                print(f"Student ID {sid} logged OUT.")
                                ser.write(b'SUCCESS_OUT\n')

                        else:
                            print("Cannot log OUT without a prior IN entry.")
                            ser.write(b'ERROR_NO_IN_RECORD\n')

                    else:
                        print("No matching record found in registaff or registudent.")
                        ser.write(b'ERROR_NO_MATCH\n')
                    
                    db.commit()  # Commit the transaction

                else:
                    print("No matching UID found in the RFID table.")
                    ser.write(b'ERROR_NO_MATCH\n')

except KeyboardInterrupt:
    print("Exiting...")
finally:
    ser.close()
    cursor.close()
    db.close()
