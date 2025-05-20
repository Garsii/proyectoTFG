#include <Wire.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <Adafruit_PN532.h>

// Pines I2C y PN532
#define SDA_PIN     21
#define SCL_PIN     22
#define PN532_IRQ    2
#define PN532_RESET  3
Adafruit_PN532 nfc(PN532_IRQ, PN532_RESET);

// WiFi
const char* ssid     = "iPhone de Alvaro";
const char* password = "tfg2025ASIR";

// Parámetros I2C/NFC
const int I2C_RECOVER_THRESHOLD = 5;
const int MAX_I2C_ATTEMPTS      = 3;
const unsigned long NFC_COOLDOWN = 5000; // 5 s

// Estado
unsigned long lastReadTime = 0;
String lastUid = "";
int i2cErrorCount = 0;

// ——— Inicializa o recupera bus I²C + PN532 ———
void initI2CAndPN532() {
  Serial.println("🔄 Recover I2C & PN532");
  Wire.end();
  delay(50);
  Wire.begin(SDA_PIN, SCL_PIN);
  Wire.setClock(50000);
  nfc.begin();
  nfc.SAMConfig();
}

// ——— Conecta WiFi ———
void connectWiFi() {
  WiFi.disconnect(true);
  WiFi.begin(ssid, password);
  Serial.print("WiFi…");
  int tries = 0;
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
    if (++tries > 20) {
      Serial.println("\nWiFi connect fail, restarting");
      ESP.restart();
    }
  }
  Serial.println(" OK, IP=" + WiFi.localIP().toString());
}

// ——— Setup ———
void setup() {
  Serial.begin(115200);
  // Si no tienes LEDs, comenta estas líneas:
  // pinMode(16, OUTPUT);
  // pinMode(17, OUTPUT);
  // pinMode(18, OUTPUT);

  Wire.begin(SDA_PIN, SCL_PIN);
  Wire.setClock(50000);
  Serial.println("I2C a 50 kHz");

  connectWiFi();

  nfc.begin();
  if (!nfc.getFirmwareVersion()) {
    Serial.println("ERROR: PN532 no detectado");
    while (true) delay(1000);
  }
  nfc.SAMConfig();
  Serial.println("PN532 listo");
}

// ——— Lee UID con recuperación de bus ———
bool readNfcUid(String &outUid) {
  uint8_t buf[7], len;
  for (int attempt = 0; attempt < MAX_I2C_ATTEMPTS; ++attempt) {
    if (nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, buf, &len)) {
      outUid = "";
      char hex[3];
      for (int i = 0; i < len; i++) {
        sprintf(hex, "%02X", buf[i]);
        outUid += hex;
      }
      i2cErrorCount = 0;
      return true;
    }
    i2cErrorCount++;
    Serial.println("I2C NACK attempt " + String(attempt + 1));
    delay(50);
    if (i2cErrorCount >= I2C_RECOVER_THRESHOLD) {
      initI2CAndPN532();
      i2cErrorCount = 0;
    }
  }
  return false;
}

// ——— Envía al servidor SIEMPRE por HTTPS ———
bool sendToServer(const String& uid) {
  if (WiFi.status() != WL_CONNECTED) connectWiFi();

  WiFiClientSecure client;
  client.setInsecure();

  HTTPClient https;
  https.setTimeout(15000);

  const char* url = "https://alvaroasir.com/api/registro-acceso";

  if (!https.begin(client, url)) {
    Serial.println("https.begin() falló");
    return false;
  }

  https.addHeader("Content-Type", "application/json");
  String body = "{\"uid\":\"" + uid + "\",\"punto_acceso_id\":1}";

  int code = https.POST(body);
  String resp = https.getString();
  Serial.println("HTTPS code: " + String(code));
  Serial.println("Response: " + resp);

  https.end();

  return (code >= 200 && code < 300);
}

// ——— Espera a que la tarjeta sea retirada del lector ———
void waitForRemoval() {
  uint8_t buf[7], len;
  // Mientras siga leyendo la misma tarjeta, espera
  while (nfc.readPassiveTargetID(PN532_MIFARE_ISO14443A, buf, &len)) {
    delay(100);
  }
}

// ——— Loop ———
void loop() {
  String uid;
  
  // Sólo intentamos leer si ha pasado el cooldown
  if (millis() - lastReadTime >= NFC_COOLDOWN) {
    if (readNfcUid(uid)) {
      lastReadTime = millis();  // marcamos el inicio del nuevo cooldown
      Serial.println("UID: " + uid);

      bool ok = sendToServer(uid);

      // Feedback: LEDs o relé si los tienes
      if (ok) {
        // digitalWrite(16, HIGH);
        // delay(200);
        // digitalWrite(16, LOW);
      } else {
        // digitalWrite(17, HIGH);
        // delay(200);
        // digitalWrite(17, LOW);
      }
    }
  }

  delay(50);
}
