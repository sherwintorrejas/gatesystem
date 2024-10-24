#include <SPI.h>
#include <MFRC522.h>

// Define RFID and other pins
#define RFID_SS_PIN 10   // RFID module's SS pin (connected to Pin 10)
#define RFID_RST_PIN 9   // RFID module's RST pin

// Define LED and Buzzer pins
#define GREEN_LED_PIN 2
#define RED_LED_PIN 3
#define BUZZER_PIN 4

// Timing constants
#define SCAN_INTERVAL 60000  // 1 minute in milliseconds

// Create instance for RFID
MFRC522 rfid(RFID_SS_PIN, RFID_RST_PIN);

// Variables for RFID scanning
byte lastUID[4] = {0};  // Store the last UID
unsigned long lastScanTime = 0;  // Time of the last scan

void setup() {
  Serial.begin(9600);
    
  // Send unique identifier to Python
  Serial.println("register");  // Send the keyword "register" on startup
  
  // Initialize SPI bus
  SPI.begin();
  // Initialize RFID reader
  rfid.PCD_Init();
  
  // Initialize LEDs and Buzzer
  pinMode(GREEN_LED_PIN, OUTPUT);
  pinMode(RED_LED_PIN, OUTPUT);
  pinMode(BUZZER_PIN, OUTPUT);
  
  // Turn on the red LED (default state)
  digitalWrite(RED_LED_PIN, HIGH);
  
  Serial.println("RFID Scanner ready.");
}

void loop() {
  // Check if a message is received from Python
  if (Serial.available() > 0) {
    String message = Serial.readStringUntil('\n');
    
    // Handle SUCCESS or ERROR_DUPLICATE messages from Python
    if (message == "SUCCESS") {
      // Success: Short beep and green LED on
      digitalWrite(RED_LED_PIN, LOW);    // Turn off the red LED
      digitalWrite(GREEN_LED_PIN, HIGH); // Turn on the green LED
      tone(BUZZER_PIN, 1000, 200);       // 1000 Hz for 200 milliseconds
      delay(1500);                       // Keep green LED on for 1.5 seconds
      digitalWrite(GREEN_LED_PIN, LOW);  // Turn off the green LED
      digitalWrite(RED_LED_PIN, HIGH);   // Turn the red LED back on
    } else if (message == "ERROR_DUPLICATE") {
      // Error: Same UID detected, blink red LED and beep twice in sync
      for (int i = 0; i < 2; i++) {
        digitalWrite(RED_LED_PIN, LOW);  // Turn off the red LED (blink)
        tone(BUZZER_PIN, 1000, 200);     // Beep for 200 milliseconds
        delay(200);                      // Wait 200 ms for the beep duration
        digitalWrite(RED_LED_PIN, HIGH); // Turn on the red LED (blink)
        delay(300);                      // Wait 300 ms before the next blink/beep
      }
      noTone(BUZZER_PIN);                // Stop the beep
    }
  }

  // Look for new RFID cards
  if (rfid.PICC_IsNewCardPresent()) {
    if (rfid.PICC_ReadCardSerial()) {
      // Card detected and UID read successfully
      Serial.print("Card UID: ");
      String uidString = "";
      for (byte i = 0; i < rfid.uid.size; i++) {
        uidString += String(rfid.uid.uidByte[i], HEX);
        if (i < rfid.uid.size - 1) {
          uidString += ""; // Separate UID bytes with a colon
        }
      }
      Serial.println(uidString); // Print the UID

      // Check if the current card is the same as the last card scanned
      bool isSameCard = true;
      for (byte i = 0; i < rfid.uid.size; i++) {
        if (rfid.uid.uidByte[i] != lastUID[i]) {
          isSameCard = false;
          break;
        }
      }

      unsigned long currentTime = millis();

      if (isSameCard && (currentTime - lastScanTime <= SCAN_INTERVAL)) {
        // Same card scanned within 1 minute - Two short beeps and red LED blinks twice
        Serial.println("Card scanned again within 1 minute.");
        for (int i = 0; i < 2; i++) {
          digitalWrite(RED_LED_PIN, LOW);  // Blink red LED off
          tone(BUZZER_PIN, 1000, 200);     // 1000 Hz for 200 milliseconds
          delay(200);
          digitalWrite(RED_LED_PIN, HIGH); // Blink red LED on
          delay(300);                      // Wait 300 ms between beeps and blinks
        }
        noTone(BUZZER_PIN);                // Stop the beep
      } else {
        // New card or card scanned after 1 minute - Short beep
        Serial.println("Card scanned successfully.");
        digitalWrite(RED_LED_PIN, LOW);   // Turn off the red LED
        digitalWrite(GREEN_LED_PIN, HIGH); // Turn on the green LED
        tone(BUZZER_PIN, 1000, 200);      // 1000 Hz for 200 milliseconds
        delay(1500);                      // Keep green LED on for 1.5 seconds
        digitalWrite(GREEN_LED_PIN, LOW); // Turn off the green LED
        digitalWrite(RED_LED_PIN, HIGH);  // Turn the red LED back on
      }

      // Send UID to Python script via Serial
      Serial.print("UID: ");
      Serial.println(uidString);

      // Update the last scan time and UID
      lastScanTime = currentTime;
      for (byte i = 0; i < rfid.uid.size; i++) {
        lastUID[i] = rfid.uid.uidByte[i];
      }

      // Halt PICC (stop communication with the card)
      rfid.PICC_HaltA();

      // Stop encryption on PCD
      rfid.PCD_StopCrypto1();
    } else {
      Serial.println("Failed to read card UID.");
      // Keep red LED on
      digitalWrite(RED_LED_PIN, HIGH);
      // Long beep
      tone(BUZZER_PIN, 1000);  // 1000 Hz
      delay(2000);             // 2 seconds
      noTone(BUZZER_PIN);      // Stop the beep
    }
  } else {
    // Ensure red LED is on if no card is detected
    digitalWrite(RED_LED_PIN, HIGH);
  }

  // Add a small delay to avoid flooding the serial monitor
  delay(1000);
}
