#include <SPI.h>
#include <MFRC522.h>

// Define RFID and other pins
#define RFID_SS_PIN 10   // RFID module's SS pin (connected to Pin 10)
#define RFID_RST_PIN 9   // RFID module's RST pin

// Define LED and Buzzer pins
#define GREEN_LED_PIN 2
#define RED_LED_PIN 3
#define BUZZER_PIN 4

// Create instance for RFID
MFRC522 rfid(RFID_SS_PIN, RFID_RST_PIN);

void setup() {
  // Initialize serial communication
  Serial.begin(9600);
Serial.println("timein");
  // Initialize the RFID reader
  SPI.begin();
  rfid.PCD_Init();

  // Set LED and Buzzer pins as outputs
  pinMode(GREEN_LED_PIN, OUTPUT);
  pinMode(RED_LED_PIN, OUTPUT);
  pinMode(BUZZER_PIN, OUTPUT);

  // Turn on the red LED by default
  digitalWrite(GREEN_LED_PIN, LOW);  // Green LED off initially
  digitalWrite(RED_LED_PIN, HIGH);   // Red LED on by default
  noTone(BUZZER_PIN);                // Buzzer off initially

  Serial.println("RFID Reader ready for timein...");
}

void loop() {
  // Look for new RFID cards
  if (rfid.PICC_IsNewCardPresent() && rfid.PICC_ReadCardSerial()) {
    String uid = getUID();  // Get the UID from the RFID card
    Serial.print("Card UID: ");
    Serial.println(uid);    // Send the UID to Python over serial

    delay(1000);  // Small delay to avoid multiple scans of the same card
  }

  // Check if data is available on the serial port from Python
  if (Serial.available() > 0) {
    String command = Serial.readStringUntil('\n');

    // Handle IN success
    if (command == "SUCCESS_IN") {
      blinkGreenLEDAndBeep(1, 100, 1); // Blink green LED once, short beep once
    }

    // Handle error for already IN
    else if (command == "ERROR_ALREADY_IN") {
      blinkRedLEDAndBeep(2, 100, 2); // Blink red LED twice, short beep twice
    }

    // Handle unmatched UID error during IN
    else if (command == "ERROR_NO_MATCH") {
      longBeep(); // Long beep
    }
  }
}

// Function to get the RFID card's UID
String getUID() {
  String uidString = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    uidString += String(rfid.uid.uidByte[i] < 0x10 ? "0" : "");
    uidString += String(rfid.uid.uidByte[i], HEX);
  }
  uidString.toUpperCase();
  return uidString;
}

// Function to blink Green LED and beep while turning off Red LED during the blink
void blinkGreenLEDAndBeep(int blinkCount, int blinkDuration, int beepCount) {
  for (int i = 0; i < blinkCount; i++) {
    digitalWrite(RED_LED_PIN, LOW);    // Turn off red LED
    digitalWrite(GREEN_LED_PIN, HIGH); // Turn on green LED
    tone(BUZZER_PIN, 1000);            // Start the buzzer at 1kHz
    delay(blinkDuration);              // Wait for the blink/beep duration
    digitalWrite(GREEN_LED_PIN, LOW);  // Turn off green LED
    noTone(BUZZER_PIN);                // Stop the buzzer
    digitalWrite(RED_LED_PIN, HIGH);   // Turn red LED back on
    delay(blinkDuration);              // Wait before next blink/beep
  }
}

// Function to blink Red LED and beep
void blinkRedLEDAndBeep(int blinkCount, int blinkDuration, int beepCount) {
  for (int i = 0; i < blinkCount; i++) {
    tone(BUZZER_PIN, 1000);            // Start the buzzer at 1kHz
    digitalWrite(RED_LED_PIN, HIGH);   // Turn on red LED
    delay(blinkDuration);              // Wait for the blink/beep duration
    digitalWrite(RED_LED_PIN, LOW);    // Turn off red LED
    noTone(BUZZER_PIN);                // Stop the buzzer
    delay(blinkDuration);              // Wait before next blink/beep
    digitalWrite(RED_LED_PIN, HIGH);   // Ensure red LED is back on
  }
}

// Function to handle a long beep
void longBeep() {
  tone(BUZZER_PIN, 1000);        // Start the buzzer at 1kHz
  digitalWrite(RED_LED_PIN, HIGH); // Keep red LED on during long beep
  delay(1000);                   // Beep for 1 second
  noTone(BUZZER_PIN);            // Stop the buzzer
}
